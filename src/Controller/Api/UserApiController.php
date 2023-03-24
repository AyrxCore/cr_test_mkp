<?php

namespace App\Controller\Api;

use App\Entity\Account;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Service\UpplerAccountService;
use App\Service\UpplerAuthenticationService;
use App\Service\UpplerBuyerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/user")]
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

    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        /**@var User $user*/
        $user = $this->getUser();
        $accounts = [];

        /**@var  Account $account*/
        foreach ($user->getAccounts() as $account) {
            if (!$account->isEnabled()) {
                continue;
            }
            $datas = $this->upplerBuyerCompanyService->getBuyerByCompanyId($account->getUpplerCompanyId());
            $serializeAccount = $normalizer->normalize($account, 'json', ['groups' => 'simpleUser']);
            $serializeAccount["upplerDatas"] = $datas;
            $accounts[] = $serializeAccount;
        }
        return new JsonResponse($accounts);
    }

    #[Route("/account/{id}/select")]
    #[ParamConverter("id", Account::class)]
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

    #[Route("/logout")]
    public function logout()
    {
        $response = new Response();
        $response->headers->clearCookie('BEARER', '/');
        $response->headers->clearCookie('refresh_token', '/');
        return $response;
    }

    #[Route("/change-password")]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em
    ) {
        $datas = json_decode($request->getContent());
        /**@var User $user*/
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
