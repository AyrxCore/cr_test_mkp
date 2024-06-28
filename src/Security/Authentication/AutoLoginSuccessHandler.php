<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Controller\ChannelAwareControllerInterface;
use App\Controller\ChannelAwareControllerTrait;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Service\UpplerAuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
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
        public EventDispatcherInterface $eventDispatcher,
        public EntityManagerInterface $entityManager,
        public JWTTokenManagerInterface $JWTManager,
        public RequestStack $requestStack,
        public UpplerAuthenticationService $upplerAuthenticationService,
        public UrlGeneratorInterface $router,
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

            if (!$account = $user->getFirstEnabledAccount($channel)) {
                throw new \Exception('No account available');
            }

            $authSuccess = $this->upplerAuthenticationService->authenticateUser(
                $account,
                !$isNeoAutoLogin
            );

            if ($authSuccess && $session->has('access_token') && !empty($session->get('access_token'))) {
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
        } catch (Exception) {
            return $response;
        }
    }
}
