<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\SettingsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class MaintenanceListener
{
    public function __construct(
        private RouterInterface $router,
        private SettingsService $settingService,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $target = $request->attributes->get('_route');

        if ($target === 'maintenance' || $target === null) {
            return;
        }

        if (!$this->settingService->isMaintenanceMode()) {
            return;
        }

        $whitelistedIps = $this->settingService->getMaintenanceWhitelistIps();

        if ($this->isWhitelistedRequest($request, $whitelistedIps)) {
            $request->attributes->set('_maintenance_banner', true);

            return;
        }

        if (\str_contains($request->getRequestUri(), '/api/')) {
            $response = new JsonResponse(
                ['maintenance' => true],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        } else {
            $url = $this->router->generate('maintenance');
            $response = new RedirectResponse($url);
        }

        $event->setResponse($response);
    }

    private function isWhitelistedRequest(Request $request, array $whitelistedIps): bool
    {
        if ($whitelistedIps === []) {
            return false;
        }

        $clientIp = $request->getClientIp();

        if ($clientIp === null) {
            return false;
        }

        $normalizedWhitelistedIps = [];
        foreach ($whitelistedIps as $whitelistedIp) {
            $normalizedWhitelistedIps[$this->normalizeIp($whitelistedIp)] = true;
        }

        return isset($normalizedWhitelistedIps[$this->normalizeIp($clientIp)]);
    }

    private function normalizeIp(string $ip): string
    {
        $normalizedIp = \trim(\strtolower($ip));

        if (\str_starts_with($normalizedIp, '::ffff:')) {
            return \substr($normalizedIp, 7);
        }

        return $normalizedIp;
    }
}
