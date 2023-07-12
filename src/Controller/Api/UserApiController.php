<?php

namespace App\Controller\Api;

use App\Entity\Account;
use App\Entity\UserInfoUpdateRequest;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Events\UserInfoUpdateEvent;
use App\Service\UpplerAccountService;
use App\Service\UpplerAuthenticationService;
use App\Service\UpplerBuyerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/user')]
class UserApiController extends AbstractController
{

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAuthenticationService $upplerAuthenticationService;

    #[Required]
    public UpplerBuyerCompanyService $upplerBuyerCompanyService;

    #[Required]
    public UpplerAccountService $upplerAccountService;

    #[Required]
    public EventDispatcherInterface $eventDispatcher;

    #[Required]
    public JWTTokenManagerInterface $JWTTokenManager;

    #[Route('/email-change/{token}', name: 'changing_email_action')]
    public function emailChanging(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $token
    ) {

        $log=$em->getRepository(UserInfoUpdateRequest::class)->findOneBy(['emailChangingToken' => $token]);
        $user=$log->getUser();
        $event = new UserInfoUpdateEvent($user);
        $this->eventDispatcher->dispatch($event);
        $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user'     => $user,
            'attribute' => 'email',
            'isIso'     => 'false',
        ]);
        if ($log) {
            $user->setEmail($log->getValue());
            $em->persist($user);
            $em->flush();
        }

        $response = new Response();
        $token = $this->JWTTokenManager->create($user);
        $response->headers->setCookie(new Cookie('BEARER', $token));
        return $this->redirect('/account/details');
    }

    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $buyerDatas = $this->upplerBuyerCompanyService->getUserBuyerDatas();
        $subAccountDatas = $this->upplerAccountService->getUserSubAccountDatas();
        $user = $normalizer->normalize($this->getUser(), 'json', ['groups' => 'simpleUser']);
        $account = $normalizer->normalize($session->get('account'), 'json', ['groups' => 'simpleUser']);
        $user['account'] = $account;
        $user['account']['subaccount'] = $subAccountDatas;
        $user['account']['buyer'] = $buyerDatas;
        return new JsonResponse($user);
    }

    #[Route('/accounts')]
    public function accounts(NormalizerInterface $normalizer): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $accounts = [];

        /** @var  Account $account */
        foreach ($user->getAccounts() as $account) {
            if (!$account->isEnabled()) {
                continue;
            }
            $datas = $this->upplerBuyerCompanyService->getBuyerByCompanyId($account->getUpplerCompanyId());
            $serializeAccount = $normalizer->normalize($account, 'json', ['groups' => 'simpleUser']);
            $serializeAccount['upplerDatas'] = $datas;
            $accounts[] = $serializeAccount;
        }
        return new JsonResponse($accounts);
    }

    #[Route('/account/{id}/select')]
    #[ParamConverter('id', Account::class)]
    public function selectAccount(NormalizerInterface $normalizer, Account $account): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $userAuth = $this->upplerAuthenticationService->authenticateUser(
            $account
        );

        if ($userAuth && $session->has('access_token') && !empty($session->get('access_token'))) {
            if (empty($account->isAcceptCGU())) {
                $event = new UserAcceptCGUEvent($account);
                $this->eventDispatcher->dispatch($event);
            }
            return new JsonResponse(['status' => 'ok']);
        }

        throw new \Exception('Vous n\'avez pas accès à ce compte');
    }

    #[Route('/logout')]
    public function logout(Request $request)
    {
        $request->getSession()->invalidate();
        $response = new Response();
        $response->headers->clearCookie('BEARER', '/');
        return $response;
    }

    #[Route('/change-password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em
    ) {
        $datas = json_decode($request->getContent());
        /**@var User $user */
        $user = $this->getUser();

        if (!$userPasswordHasher->isPasswordValid($user, $datas->currentPassword)) {
            return new JsonResponse(['current password invalid'], Response::HTTP_BAD_REQUEST);
        }

        if ($datas->password !== $datas->confirmation) {
            return new JsonResponse(['password and its confirmation must be identical'], Response::HTTP_BAD_REQUEST);
        }

        $newHashedPassword = $userPasswordHasher->hashPassword($user, $datas->password);
        $user->setPassword($newHashedPassword);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(['password changed'], Response::HTTP_OK);
    }

}
