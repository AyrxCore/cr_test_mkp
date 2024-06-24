<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AutoLoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        public UrlGeneratorInterface $router,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->router->generate('prehome'));
    }
}
