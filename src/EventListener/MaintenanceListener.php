<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\SettingsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class MaintenanceListener
{
    public function __construct(
        private RouterInterface $router,
        private SettingsService $settingService
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $target = $request->attributes->get('_route');

        if ($target === 'maintenance' || $target === null) {
            return;
        }

        if ($this->settingService->isMaintenanceMode()) {
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
    }
}
