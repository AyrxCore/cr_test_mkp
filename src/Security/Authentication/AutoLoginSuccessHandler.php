<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use App\Events\UserAcceptCGUEvent;
use App\Service\UpplerAuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AutoLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    #[Required]
    public EventDispatcherInterface $eventDispatcher;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public JWTTokenManagerInterface $JWTManager;

    #[Required]
    public UrlGeneratorInterface $router;

    #[Required]
    public UpplerAuthenticationService $upplerAuthenticationService;

    public function __construct()
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $session = $this->requestStack->getSession();
        /** @var User $user */
        $user = $token->getUser();
        $token = $this->JWTManager->create($user);
        $selectedAccount = null;
        foreach ($user->getAccounts() as $account) {
            if (!$account->isEnabled()) {
                continue;
            }
            $selectedAccount = $account;
            break;
        }

        $response = new RedirectResponse($this->router->generate('prehome'));

        if ($selectedAccount !== null) {
            $authSuccess = $this->upplerAuthenticationService->authenticateUser(
                $account
            );

            if ($authSuccess && $session->has('access_token') && !empty($session->get('access_token'))) {
                if (empty($account->isAcceptCGU())) {
                    $event = new UserAcceptCGUEvent($account);
                    $this->eventDispatcher->dispatch($event);
                }
                $user->setIsEnabled(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $response->headers->setCookie(new Cookie('BEARER', $token));
            }
        }

        return $response;
    }
}
