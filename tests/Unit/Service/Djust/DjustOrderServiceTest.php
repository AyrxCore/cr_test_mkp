<?php

declare(strict_types=1);

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\DjustOrderService;
use App\Service\Djust\DjustStoreViewHeadersBuilder;

\uses()->group('UnitDjustOrderService', 'UnitOrder');

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->storeViewHeaders = ['dj-store-view' => 'default'];
    $this->storeViewHeadersBuilder = Mockery::mock(DjustStoreViewHeadersBuilder::class);
    $this->storeViewHeadersBuilder->shouldReceive('build')->andReturn($this->storeViewHeaders);
    $this->service = new DjustOrderService($this->httpClient, $this->storeViewHeadersBuilder);
});

\afterEach(function () {
    Mockery::close();
});

\it('fetches orders successfully', function () {
    $expectedResponse = [
        'content' => [
            ['id' => '1', 'orderLogistics' => [['id' => 'log1']]],
            ['id' => '2', 'orderLogistics' => [['id' => 'log2']]],
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            ['locale' => DjustDefaults::LOCALE->value],
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrders();

    \expect($result)->toHaveCount(2);
    \expect($result[0])->toHaveKey('orderLogistics');
});

\it('fetches orders with custom params', function () {
    $expectedResponse = [
        'content' => [
            ['id' => '1', 'orderLogistics' => [['id' => 'log1']]],
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            [
                'locale' => DjustDefaults::LOCALE->value,
                'sort' => 'createdAt:desc',
            ],
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrders(['sort' => 'createdAt:desc']);

    \expect($result)->toHaveCount(1);
});

\it('filters out orders without orderLogistics', function () {
    $expectedResponse = [
        'content' => [
            ['id' => '1', 'orderLogistics' => [['id' => 'log1']]],
            ['id' => '2', 'orderLogistics' => []],
            ['id' => '3'],
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            Mockery::any(),
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrders();

    \expect($result)->toHaveCount(1);
    \expect($result[0]['id'])->toBe('1');
});

\it('returns empty array when no content', function () {
    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            Mockery::any(),
            $this->storeViewHeaders,
        )
        ->andReturn([]);

    $result = $this->service->getOrders();

    \expect($result)->toBe([]);
});

\it('fetches order by id successfully', function () {
    $orderId = '12345';
    $formattedId = '0000012345';
    $expectedResponse = [
        'id' => $formattedId,
        'orderLogistics' => [['id' => 'log1']],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BY_ID->value, $formattedId),
            ['locale' => DjustDefaults::LOCALE->value],
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrderById($orderId);

    \expect($result)->not()->toBeNull();
    \expect($result['id'])->toBe($formattedId);
});

\it('pads order id correctly', function () {
    $orderId = '123';
    $formattedId = '0000000123';
    $expectedResponse = ['orderLogistics' => [['id' => 'log1']]];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BY_ID->value, $formattedId),
            Mockery::any(),
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrderById($orderId);

    \expect($result)->not()->toBeNull();
});

\it('returns null when order has no orderLogistics', function () {
    $orderId = '12345';
    $formattedId = '0000012345';
    $expectedResponse = [
        'id' => $formattedId,
        'orderLogistics' => [],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            Mockery::any(),
            Mockery::any(),
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrderById($orderId);

    \expect($result)->toBeNull();
});

\it('returns null when order response has no orderLogistics key', function () {
    $orderId = '12345';
    $expectedResponse = ['id' => '0000012345'];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            Mockery::any(),
            Mockery::any(),
            $this->storeViewHeaders,
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getOrderById($orderId);

    \expect($result)->toBeNull();
});
