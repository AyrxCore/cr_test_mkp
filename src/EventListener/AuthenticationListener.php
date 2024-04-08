<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\AccessMapInterface;

class AuthenticationListener
{
    private const NOT_HYDRATED_ROUTES = [
        'api_accounts_get_collection',
        'account_select',
    ];

    public function __construct(
        private AccessMapInterface $accessMap,
        private RequestStack $requestStack,
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $target = $request->attributes->get('_route');
        $access = $this->accessMap->getPatterns($request);

        if (\in_array($target, self::NOT_HYDRATED_ROUTES, true) || !\in_array('IS_AUTHENTICATED_FULLY', $access[0], true)) {
            return;
        }

        $session = $this->requestStack->getSession();

        if (!$session->has('account') || empty($session->get('account'))) {
            throw new AuthenticationException('session account is not hydrated');
        }
    }
}
