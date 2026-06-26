<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserInfoUpdateRequest;
use App\Events\UserInfoUpdateEvent;
use App\Service\UpplerAccountService;
use App\Service\UpplerAuthenticationService;
use App\Service\UpplerBuyerCompanyService;
use App\Validator\PasswordStrength;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user')]
class UserApiController extends AbstractController
{
    public function __construct(
        public RequestStack $requestStack,
        public UpplerAuthenticationService $upplerAuthenticationService,
        public UpplerBuyerCompanyService $upplerBuyerCompanyService,
        public UpplerAccountService $upplerAccountService,
        private EntityManagerInterface $em,
        public EventDispatcherInterface $eventDispatcher,
        public JWTTokenManagerInterface $JWTTokenManager,
    ) {
    }

    #[Route('/email-change/{token}', name: 'changing_email_action')]
    public function emailChanging(string $token): RedirectResponse
    {
        $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy(['emailChangingToken' => $token]);
        $user = $log->getUser();
        $event = new UserInfoUpdateEvent($user);
        $this->eventDispatcher->dispatch($event);
        $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user' => $user,
            'attribute' => 'email',
            'isIso' => 'false',
        ]);
        if ($log) {
            $user->setEmail($log->getValue());
            $this->em->persist($user);
            $this->em->flush();
        }

        $response = new Response();
        $token = $this->JWTTokenManager->create($user);
        $response->headers->setCookie(new Cookie('BEARER', $token));

        return $this->redirect('/account/details');
    }

    #[Route('/logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();
        $response = new Response();
        $response->headers->clearCookie('BEARER');
        $response->headers->clearCookie('neoAutoLogin');
        $response->headers->clearCookie('PHPSESSID');

        return $response;
    }

    // TODO: replace with a custom operation => https://api-platform.com/docs/v2.6/core/controllers/
    #[Route('/change-password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): JsonResponse {
        $data = \json_decode($request->getContent());
        if (!$data || !isset($data->currentPassword, $data->password, $data->confirmation)) {
            return new JsonResponse(['missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();

        if (!$userPasswordHasher->isPasswordValid($user, $data->currentPassword)) {
            return new JsonResponse(['current password invalid'], Response::HTTP_BAD_REQUEST);
        }

        if ($data->password !== $data->confirmation) {
            return new JsonResponse(['password and its confirmation must be identical'], Response::HTTP_BAD_REQUEST);
        }

        $violations = $validator->validate($data->password, new PasswordStrength());
        if (\count($violations) > 0) {
            return new JsonResponse([(string) $violations->get(0)->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $newHashedPassword = $userPasswordHasher->hashPassword($user, $data->password);
        $user->setPassword($newHashedPassword);
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['password changed'], Response::HTTP_OK);
    }
}
