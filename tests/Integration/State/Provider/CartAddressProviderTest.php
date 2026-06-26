<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\CartAddress;
use App\State\Provider\CartAddressProvider;

\uses()->group('IntegrationCartAddressProvider');

\it('retrieves CartAddress provider from container', function () {
    $provider = $this->container->get(CartAddressProvider::class);

    \expect($provider)->toBeInstanceOf(CartAddressProvider::class);
});

\it('provides CartAddress with valid cartId from URI variables', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();
    $uriVariables = ['cartId' => 'valid-cart-123'];

    $result = $provider->provide($operation, $uriVariables);

    \expect($result)->toBeInstanceOf(CartAddress::class)
        ->and($result->getCartId())->toBe('valid-cart-123');
});

\it('initializes CartAddress with null address external IDs', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();

    $result = $provider->provide($operation, ['cartId' => 'test-cart']);

    \expect($result->getBillingAddressExternalId())->toBeNull()
        ->and($result->getShippingAddressExternalId())->toBeNull();
});

\it('creates new CartAddress instance on each call', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();

    $result1 = $provider->provide($operation, ['cartId' => 'cart-1']);
    $result2 = $provider->provide($operation, ['cartId' => 'cart-1']);

    \expect($result1)->not->toBe($result2)
        ->and(\spl_object_id($result1))->not->toBe(\spl_object_id($result2));
});

\it('handles different cart IDs independently', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();

    $cartA = $provider->provide($operation, ['cartId' => 'cart-A']);
    $cartB = $provider->provide($operation, ['cartId' => 'cart-B']);
    $cartC = $provider->provide($operation, ['cartId' => 'cart-C']);

    \expect($cartA->getCartId())->toBe('cart-A')
        ->and($cartB->getCartId())->toBe('cart-B')
        ->and($cartC->getCartId())->toBe('cart-C');
});

\it('accepts UUID format for cartId', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();
    $uuid = '550e8400-e29b-41d4-a716-446655440000';

    $result = $provider->provide($operation, ['cartId' => $uuid]);

    \expect($result->getCartId())->toBe($uuid);
});

\it('accepts alphanumeric cartId with hyphens', function () {
    $provider = $this->container->get(CartAddressProvider::class);
    $operation = new Get();
    $cartId = 'cart-2024-abc-123';

    $result = $provider->provide($operation, ['cartId' => $cartId]);

    \expect($result->getCartId())->toBe($cartId);
});
