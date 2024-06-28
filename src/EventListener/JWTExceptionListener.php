<?php

declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

readonly class JWTExceptionListener
{
    public function __construct()
    {
    }

    public function onJWTException(JWTExpiredEvent|JWTInvalidEvent $event): void
    {
        $response = new RedirectResponse($event->getRequest()->getRequestUri());
        $response->headers->clearCookie('BEARER');
        $event->setResponse($response);
    }
}
