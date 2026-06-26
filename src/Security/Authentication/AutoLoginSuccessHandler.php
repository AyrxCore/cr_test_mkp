<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Controller\ChannelAwareControllerInterface;
use App\Controller\ChannelAwareControllerTrait;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Repository\AccountRepository;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustAuthenticationService;
use App\Service\UpplerAuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AutoLoginSuccessHandler implements AuthenticationSuccessHandlerInterface, ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTTokenManagerInterface $JWTManager,
        private readonly RequestStack $requestStack,
        private readonly UpplerAuthenticationService $upplerAuthenticationService,
        private readonly UrlGeneratorInterface $router,
        private readonly DjustAuthenticationService $djustAuthenticationService,
        protected readonly LoggerInterface $djustLogger,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $session = $this->requestStack->getSession();
        /** @var User $user */
        $user = $token->getUser();
        $token = $this->JWTManager->create($user);
        $isNeoAutoLogin = \filter_var($request->query->get('neoAutoLogin'), \FILTER_VALIDATE_BOOLEAN);

        try {
            $response = new RedirectResponse($this->router->generate('prehome'));

            if (!$channel = $this->getChannel($request)) {
                throw new \Exception();
            }

            if ($isNeoAutoLogin) {
                $account = $this->accountRepository->findOneBy([
                    'enabled' => true,
                    'user' => $user,
                    'contactId' => $request->query->get('contactId'),
                ]);
            } else {
                $account = $user->getFirstEnabledAccount($channel);
            }

            if (!$account) {
                throw new \Exception('No account available');
            }

            $session = $this->requestStack->getSession();
            $session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, $account);

            $authUpplerSuccess = $this->upplerAuthenticationService->authenticateUser(
                $account,
                !$isNeoAutoLogin
            );

            try {
                //                TODO: Adaptation DJUST - authentification
                $authDjustSuccess = $this->djustAuthenticationService->authenticateUser($account, !$isNeoAutoLogin);
            } catch (\Throwable $e) {
                $this->djustLogger->warning("Erreur lors de l'authentification Djust pour cet account :  ".$account->getId());
            }

            if ($authUpplerSuccess && $session->has('access_token') && !empty($session->get('access_token'))) {
                if (empty($account->isAcceptCGU()) && !$isNeoAutoLogin) {
                    $event = new UserAcceptCGUEvent($account);
                    $this->eventDispatcher->dispatch($event);
                }
                $user->setEnabled(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $response->headers->setCookie(new Cookie('BEARER', $token));
                if ($isNeoAutoLogin) {
                    $response->headers->setCookie(new Cookie('neoAutoLogin', 'true', httpOnly: false));
                }
            }

            return $response;
        } catch (\Exception) {
            return $response;
        }
    }
}
