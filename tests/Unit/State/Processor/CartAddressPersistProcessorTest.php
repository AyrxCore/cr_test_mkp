<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\CartAddress;
use App\Service\Djust\DjustCartService;
use App\State\Processor\CartAddressPersistProcessor;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

\uses()->group('UnitCartAddressPersistProcessor');

\beforeEach(function () {
    $this->djustCartService = \Mockery::mock(DjustCartService::class);
    $this->processor = new CartAddressPersistProcessor($this->djustCartService);
});

\afterEach(function () {
    \Mockery::close();
});

\it('should update billing address only when billing address is provided', function () {
    $cartId = 'cart-123';
    $billingAddressExternalId = 'billing-456';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->twice()->andReturn($billingAddressExternalId);
    $data->shouldReceive('getShippingAddressExternalId')->once()->andReturn(null);

    $this->djustCartService
        ->shouldReceive('updateCartBillingAddress')
        ->once()
        ->with($cartId, $billingAddressExternalId)
        ->andReturn([]);

    $this->djustCartService
        ->shouldNotReceive('updateCartShippingAddress');

    $operation = new Patch();

    $result = $this->processor->process($data, $operation);

    \expect($result)->toBe($data);
});

\it('should update shipping address only when shipping address is provided', function () {
    $cartId = 'cart-123';
    $shippingAddressExternalId = 'shipping-789';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->once()->andReturn(null);
    $data->shouldReceive('getShippingAddressExternalId')->twice()->andReturn($shippingAddressExternalId);

    $this->djustCartService
        ->shouldNotReceive('updateCartBillingAddress');

    $this->djustCartService
        ->shouldReceive('updateCartShippingAddress')
        ->once()
        ->with($cartId, $shippingAddressExternalId)
        ->andReturn([]);

    $operation = new Patch();

    $result = $this->processor->process($data, $operation);

    \expect($result)->toBe($data);
});

\it('should update both addresses when both are provided', function () {
    $cartId = 'cart-123';
    $billingAddressExternalId = 'billing-456';
    $shippingAddressExternalId = 'shipping-789';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->twice()->andReturn($billingAddressExternalId);
    $data->shouldReceive('getShippingAddressExternalId')->twice()->andReturn($shippingAddressExternalId);

    $this->djustCartService
        ->shouldReceive('updateCartBillingAddress')
        ->once()
        ->with($cartId, $billingAddressExternalId)
        ->andReturn([]);

    $this->djustCartService
        ->shouldReceive('updateCartShippingAddress')
        ->once()
        ->with($cartId, $shippingAddressExternalId)
        ->andReturn([]);

    $operation = new Patch();

    $result = $this->processor->process($data, $operation);

    \expect($result)->toBe($data);
});

\it('should not update any address when both are null', function () {
    $cartId = 'cart-123';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->once()->andReturn(null);
    $data->shouldReceive('getShippingAddressExternalId')->once()->andReturn(null);

    $this->djustCartService
        ->shouldNotReceive('updateCartBillingAddress');

    $this->djustCartService
        ->shouldNotReceive('updateCartShippingAddress');

    $operation = new Patch();

    $result = $this->processor->process($data, $operation);

    \expect($result)->toBe($data);
});

\it('should throw BadRequestException when operation is not Patch', function () {
    $data = \Mockery::mock(CartAddress::class);

    $operation = new Post();

    $this->djustCartService
        ->shouldNotReceive('updateCartBillingAddress');

    $this->djustCartService
        ->shouldNotReceive('updateCartShippingAddress');

    \expect(fn () => $this->processor->process($data, $operation))
        ->toThrow(BadRequestException::class, 'Only PATCH operation is supported');
});

\it('should throw BadRequestException when djustCartService throws exception on billing address update', function () {
    $cartId = 'cart-123';
    $billingAddressExternalId = 'billing-456';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->twice()->andReturn($billingAddressExternalId);

    $this->djustCartService
        ->shouldReceive('updateCartBillingAddress')
        ->once()
        ->with($cartId, $billingAddressExternalId)
        ->andThrow(new \Exception('Service error'));

    $operation = new Patch();

    \expect(fn () => $this->processor->process($data, $operation))
        ->toThrow(BadRequestHttpException::class);
});

\it('should throw BadRequestException when djustCartService throws exception on shipping address update', function () {
    $cartId = 'cart-123';
    $shippingAddressExternalId = 'shipping-789';

    $data = \Mockery::mock(CartAddress::class);
    $data->shouldReceive('getCartId')->once()->andReturn($cartId);
    $data->shouldReceive('getBillingAddressExternalId')->once()->andReturn(null);
    $data->shouldReceive('getShippingAddressExternalId')->twice()->andReturn($shippingAddressExternalId);

    $this->djustCartService
        ->shouldReceive('updateCartShippingAddress')
        ->once()
        ->with($cartId, $shippingAddressExternalId)
        ->andThrow(new \Exception('Service error'));

    $operation = new Patch();

    \expect(fn () => $this->processor->process($data, $operation))
        ->toThrow(BadRequestHttpException::class);
});
