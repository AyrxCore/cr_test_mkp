<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\Order;
use App\Factory\OrderFactory;
use App\Service\Djust\DjustOrderService;
use App\State\Provider\OrderProvider;

\uses()->group('UnitOrderProvider', 'UnitOrder');

\beforeEach(function () {
    $this->djustOrderService = Mockery::mock(DjustOrderService::class);
    $this->orderFactory = Mockery::mock(OrderFactory::class);

    $this->provider = new OrderProvider(
        $this->djustOrderService,
        $this->orderFactory
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('provides collection of orders', function () {
    $operation = new GetCollection();
    $remoteOrders = [
        ['id' => '1', 'orderLogistics' => [['status' => 'CONFIRMED']]],
        ['id' => '2', 'orderLogistics' => [['status' => 'SHIPPED']]],
    ];

    $order1 = new Order();
    $order1->setId(1);
    $order2 = new Order();
    $order2->setId(2);

    $this->djustOrderService
        ->shouldReceive('getOrders')
        ->once()
        ->with(['sort' => 'createdAt:desc'])
        ->andReturn($remoteOrders);

    $this->orderFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($remoteOrders)
        ->andReturn([$order1, $order2]);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeArray();
    \expect($result)->toHaveCount(2);
    \expect($result[0])->toBeInstanceOf(Order::class);
});

\it('filters out new/draft orders from collection', function () {
    $operation = new GetCollection();

    $visibleOrder = ['id' => '1', 'orderLogistics' => [['status' => 'CONFIRMED']]];
    $remoteOrders = [
        $visibleOrder,
        ['id' => '2', 'orderLogistics' => [['status' => 'CREATING']]],
        ['id' => '3', 'orderLogistics' => [['status' => 'DRAFT_ORDER']]],
        ['id' => '4', 'orderLogistics' => [['status' => 'DRAFT_ORDER_ON_HOLD']]],
    ];

    $order1 = new Order();
    $order1->setId(1);

    $this->djustOrderService
        ->shouldReceive('getOrders')
        ->once()
        ->andReturn($remoteOrders);

    $this->orderFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([$visibleOrder])
        ->andReturn([$order1]);

    $result = $this->provider->provide($operation);

    \expect($result)->toHaveCount(1);
    \expect($result[0]->getId())->toBe(1);
});

\it('provides single order by id', function () {
    $operation = new Get();
    $uriVariables = ['id' => 12345];

    $remoteOrder = [
        'id' => '0000012345',
        'orderLogistics' => [['id' => 'log1', 'status' => 'CONFIRMED']],
    ];

    $order = new Order();
    $order->setId(12345);

    $this->djustOrderService
        ->shouldReceive('getOrderById')
        ->once()
        ->with('12345')
        ->andReturn($remoteOrder);

    $this->orderFactory
        ->shouldReceive('create')
        ->once()
        ->with($remoteOrder)
        ->andReturn($order);

    $result = $this->provider->provide($operation, $uriVariables);

    \expect($result)->toBeInstanceOf(Order::class);
    \expect($result->getId())->toBe(12345);
});

\it('returns null when order not found', function () {
    $operation = new Get();
    $uriVariables = ['id' => 99999];

    $this->djustOrderService
        ->shouldReceive('getOrderById')
        ->once()
        ->with('99999')
        ->andReturn(null);

    $this->orderFactory
        ->shouldReceive('create')
        ->never();

    $result = $this->provider->provide($operation, $uriVariables);

    \expect($result)->toBeNull();
});

\it('returns null when single order has a hidden status', function () {
    $operation = new Get();
    $uriVariables = ['id' => 12345];

    $this->djustOrderService
        ->shouldReceive('getOrderById')
        ->once()
        ->with('12345')
        ->andReturn(['id' => '0000012345', 'orderLogistics' => [['status' => 'DRAFT_ORDER']]]);

    $this->orderFactory
        ->shouldReceive('create')
        ->never();

    $result = $this->provider->provide($operation, $uriVariables);

    \expect($result)->toBeNull();
});

\it('handles empty collection', function () {
    $operation = new GetCollection();

    $this->djustOrderService
        ->shouldReceive('getOrders')
        ->once()
        ->andReturn([]);

    $this->orderFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([])
        ->andReturn([]);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeArray();
    \expect($result)->toBeEmpty();
});

\it('handles collection where all orders are hidden', function () {
    $operation = new GetCollection();

    $this->djustOrderService
        ->shouldReceive('getOrders')
        ->once()
        ->andReturn([
            ['id' => '1', 'orderLogistics' => [['status' => 'CREATING']]],
            ['id' => '2', 'orderLogistics' => [['status' => 'DRAFT_ORDER']]],
        ]);

    $this->orderFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([])
        ->andReturn([]);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeEmpty();
});
