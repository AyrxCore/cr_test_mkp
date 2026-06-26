<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\CartAddress;
use App\State\Provider\CartAddressProvider;

\uses()->group('UnitCartAddressProvider');

\beforeEach(function () {
    $this->provider = new CartAddressProvider();
});

\it('provides a CartAddress object with cartId from URI variables', function () {
    $operation = new Get();
    $uriVariables = ['cartId' => 'cart-123'];

    $result = $this->provider->provide($operation, $uriVariables);

    \expect($result)->toBeInstanceOf(CartAddress::class)
        ->and($result->getCartId())->toBe('cart-123');
});

\it('provides CartAddress with correct cart ID through service', function () {
    $operation = new Get();
    $uriVariables = ['cartId' => 'integration-cart-789'];

    $result = $this->provider->provide($operation, $uriVariables);

    \expect($result)->toBeInstanceOf(CartAddress::class)
        ->and($result->getCartId())->toBe('integration-cart-789')
        ->and($result->getBillingAddressExternalId())->toBeNull()
        ->and($result->getShippingAddressExternalId())->toBeNull();
});
