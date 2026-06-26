<?php

declare(strict_types=1);

use App\EventListener\MaintenanceListener;
use App\Service\SettingsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

function makeEvent(Request $request): RequestEvent
{
    $kernel = Mockery::mock(HttpKernelInterface::class);

    return new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);
}

function makeListener(bool $maintenanceActive, array $whitelistedIps = []): MaintenanceListener
{
    $router = Mockery::mock(RouterInterface::class);
    $router->shouldReceive('generate')->with('maintenance')->andReturn('/maintenance')->byDefault();

    $settingsService = Mockery::mock(SettingsService::class);
    $settingsService->shouldReceive('isMaintenanceMode')->andReturn($maintenanceActive);
    $settingsService->shouldReceive('getMaintenanceWhitelistIps')->andReturn($whitelistedIps);

    return new MaintenanceListener($router, $settingsService);
}

\it('does nothing when maintenance mode is disabled', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $event = \makeEvent($request);

    \makeListener(false)->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
})->group('UnitMaintenanceListener');

\it('does nothing for the maintenance route itself', function () {
    $request = Request::create('/maintenance');
    $request->attributes->set('_route', 'maintenance');
    $event = \makeEvent($request);

    \makeListener(true)->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
})->group('UnitMaintenanceListener');

\it('redirects to maintenance page for non-whitelisted IP on HTML request', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $event = \makeEvent($request);

    \makeListener(true, ['1.1.1.1'])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeTrue();
    \expect($event->getResponse())->toBeInstanceOf(RedirectResponse::class);
    \expect($event->getResponse()->getStatusCode())->toBe(Response::HTTP_FOUND);
})->group('UnitMaintenanceListener');

\it('returns 503 JSON for non-whitelisted IP on API request', function () {
    $request = Request::create('/api/products');
    $request->attributes->set('_route', 'api_products');
    $event = \makeEvent($request);

    \makeListener(true, ['1.1.1.1'])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeTrue();
    \expect($event->getResponse())->toBeInstanceOf(JsonResponse::class);
    \expect($event->getResponse()->getStatusCode())->toBe(Response::HTTP_SERVICE_UNAVAILABLE);

    $data = \json_decode($event->getResponse()->getContent(), true);
    \expect($data)->toBe(['maintenance' => true]);
})->group('UnitMaintenanceListener');

\it('bypasses maintenance and sets banner flag for whitelisted IP', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $request->server->set('REMOTE_ADDR', '154.49.230.146');
    $event = \makeEvent($request);

    \makeListener(true, ['154.49.230.146'])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
    \expect($request->attributes->get('_maintenance_banner'))->toBeTrue();
})->group('UnitMaintenanceListener');

\it('bypasses maintenance when client IP is IPv4-mapped IPv6', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $request->server->set('REMOTE_ADDR', '::ffff:176.147.223.70');
    $event = \makeEvent($request);

    \makeListener(true, ['176.147.223.70'])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
    \expect($request->attributes->get('_maintenance_banner'))->toBeTrue();
})->group('UnitMaintenanceListener');

\it('does not set banner flag when maintenance is disabled even for whitelisted IP', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $request->server->set('REMOTE_ADDR', '154.49.230.146');
    $event = \makeEvent($request);

    \makeListener(false, ['154.49.230.146'])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
    \expect($request->attributes->get('_maintenance_banner'))->toBeNull();
})->group('UnitMaintenanceListener');

\it('blocks access when whitelist is empty and maintenance is active', function () {
    $request = Request::create('/');
    $request->attributes->set('_route', 'prehome');
    $event = \makeEvent($request);

    \makeListener(true, [])->onKernelRequest($event);

    \expect($event->hasResponse())->toBeTrue();
    \expect($event->getResponse())->toBeInstanceOf(RedirectResponse::class);
})->group('UnitMaintenanceListener');
