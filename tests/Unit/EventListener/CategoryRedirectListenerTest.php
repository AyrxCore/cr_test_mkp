<?php

declare(strict_types=1);

use App\EventListener\CategoryRedirectListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Yaml\Yaml;

function makeCategoryRedirectEvent(Request $request): RequestEvent
{
    $kernel = Mockery::mock(HttpKernelInterface::class);

    return new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);
}

function makeCategoryRedirectListener(array $categoryRedirects = ['1309' => '0013400126']): CategoryRedirectListener
{
    return new CategoryRedirectListener($categoryRedirects);
}

\it('redirects a known legacy Uppler category id to the matching Djust id', function () {
    $request = Request::create('/products?category=1309');
    $event = \makeCategoryRedirectEvent($request);

    \makeCategoryRedirectListener()->onKernelRequest($event);

    \expect($event->hasResponse())->toBeTrue();
    \expect($event->getResponse())->toBeInstanceOf(RedirectResponse::class);
    \expect($event->getResponse()->getStatusCode())->toBe(Response::HTTP_MOVED_PERMANENTLY);
    \expect($event->getResponse()->headers->get('Location'))->toBe('/products?category=0013400126');
})->group('UnitCategoryRedirectListener');

\it('preserves other query params when redirecting', function () {
    $request = Request::create('/products?category=1309&sort=price');
    $event = \makeCategoryRedirectEvent($request);

    \makeCategoryRedirectListener()->onKernelRequest($event);

    $location = $event->getResponse()->headers->get('Location');
    \expect($location)->not->toBeNull();

    \parse_str((string) \parse_url($location, PHP_URL_QUERY), $query);
    \expect($query['category'])->toBe('0013400126');
    \expect($query['sort'])->toBe('price');
})->group('UnitCategoryRedirectListener');

\it('does nothing when the category id is not in the mapping', function () {
    $request = Request::create('/products?category=0013400024');
    $event = \makeCategoryRedirectEvent($request);

    \makeCategoryRedirectListener()->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
})->group('UnitCategoryRedirectListener');

\it('does nothing when there is no category query param', function () {
    $request = Request::create('/products');
    $event = \makeCategoryRedirectEvent($request);

    \makeCategoryRedirectListener()->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
})->group('UnitCategoryRedirectListener');

\it('does nothing for paths other than /products', function () {
    $request = Request::create('/accord-cadres/fat-peugeot-2?category=1309');
    $event = \makeCategoryRedirectEvent($request);

    \makeCategoryRedirectListener()->onKernelRequest($event);

    \expect($event->hasResponse())->toBeFalse();
})->group('UnitCategoryRedirectListener');

\it('has no redirect chain in the real mapping data (no target id is itself a legacy key)', function () {
    $config = Yaml::parseFile(__DIR__.'/../../../config/uppler_category_redirects.yaml');
    $categoryRedirects = $config['parameters']['uppler_category_redirects'];

    $cycles = \array_intersect(\array_keys($categoryRedirects), \array_values($categoryRedirects));

    \expect($cycles)->toBe([]);
})->group('UnitCategoryRedirectListener');
