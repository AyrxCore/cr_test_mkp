<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\Cart;
use App\Factory\DjustCartFactory;
use App\Service\Djust\DjustCartService;
use App\State\Provider\CartProvider;

\uses()->group('UnitCartProvider');

\beforeEach(function () {
    $this->djustCartService = Mockery::mock(DjustCartService::class);
    $this->djustCartFactory = Mockery::mock(DjustCartFactory::class);

    $this->provider = new CartProvider(
        $this->djustCartService,
        $this->djustCartFactory
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('provides a Cart object from commercial order', function () {
    $operation = new Get();
    $commercialOrder = [
        'id' => 'cart-123',
        'totalPrice' => 100.50,
    ];
    $cart = new Cart();
    $cart->setId('cart-123');

    $this->djustCartService
        ->shouldReceive('getCart')
        ->once()
        ->andReturn($commercialOrder);

    $this->djustCartFactory
        ->shouldReceive('createFromCommercialOrder')
        ->once()
        ->with($commercialOrder)
        ->andReturn($cart);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeInstanceOf(Cart::class)
        ->and($result->getId())->toBe('cart-123');
});

\it('passes empty URI variables and context to dependencies', function () {
    $operation = new Get();
    $uriVariables = [];
    $context = [];
    $commercialOrder = ['id' => 'cart-456'];
    $cart = new Cart();

    $this->djustCartService
        ->shouldReceive('getCart')
        ->once()
        ->andReturn($commercialOrder);

    $this->djustCartFactory
        ->shouldReceive('createFromCommercialOrder')
        ->once()
        ->with($commercialOrder)
        ->andReturn($cart);

    $result = $this->provider->provide($operation, $uriVariables, $context);

    \expect($result)->toBeInstanceOf(Cart::class);
});
