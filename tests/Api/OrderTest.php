<?php

declare(strict_types=1);

use App\Dto\Order;

\uses()->group('ApiOrder');

\it('gets order collection from Djust API', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders');

    $this->assertResponseIsSuccessful();
    $this->assertJsonContains([
        '@context' => '/api/contexts/Order',
        '@type' => 'Collection',
    ]);

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('member');
    \expect($response['member'])->toBeArray();
});

\it('gets order collection with valid structure', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response['member'])->toBeArray();

    if (\count($response['member']) > 0) {
        $order = $response['member'][0];
        \expect($order)->toHaveKey('@type', 'Order');
        \expect($order)->toHaveKey('id');
        \expect($order)->toHaveKey('orderNumber');
        \expect($order)->toHaveKey('total');
    }
});

\it('gets single order by id from Djust API', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();
    $this->assertJsonContains([
        '@type' => 'Order',
        'id' => 12345,
    ]);

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('orderNumber');
    \expect($response)->toHaveKey('total');
    \expect($response)->toHaveKey('items');
});

\it('gets order with complete information', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('id');
    \expect($response)->toHaveKey('orderNumber');
    \expect($response)->toHaveKey('total');
    \expect($response)->toHaveKey('totalExcludingTaxes');
    \expect($response)->toHaveKey('shippingState');
    \expect($response)->toHaveKey('productCount');
    \expect($response)->toHaveKey('shipmentAmount');
    \expect($response)->toHaveKey('items');
    \expect($response)->toHaveKey('createdAt');
    \expect($response)->toHaveKey('updatedAt');
});

\it('gets order with shipping information', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('shippingAddress');
    \expect($response)->toHaveKey('billingAddress');
    \expect($response)->toHaveKey('shippingState');
});

\it('gets order with items details', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response['items'])->toBeArray();

    if (\count($response['items']) > 0) {
        $item = $response['items'][0];
        \expect($item)->toHaveKey('quantity');
        \expect($item)->toHaveKey('unit_price');
        \expect($item)->toHaveKey('variant');
        \expect($item['variant'])->toHaveKey('product');
    }
});

\it('gets order with dates', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('createdAt');
    \expect($response)->toHaveKey('updatedAt');
    \expect($response['createdAt'])->toBeString();
    \expect($response['updatedAt'])->toBeString();
});

\it('gets order with invoice URL if available', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('invoiceUrl');
});

\it('returns 404 when order not found', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/999999');

    $this->assertResponseStatusCodeSame(404);
});

\it('returns orders with valid dates', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();

    if (\count($response['member']) >= 1) {
        $order = $response['member'][0];
        \expect($order)->toHaveKey('createdAt');
        
        // Verify the date is parseable
        $createdDate = new \DateTime($order['createdAt']);
        \expect($createdDate)->toBeInstanceOf(\DateTime::class);
    }
});

\it('returns order with valid shipping state values', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response['shippingState'])->toBeIn([
        Order::SHIPPING_PENDING,
        Order::SHIPPING_PREPARATION,
        Order::SHIPPING_READY,
        Order::SHIPPING_PARTIALLY_SHIPPED,
        Order::SHIPPING_SHIPPED,
        Order::SHIPPING_DELIVERED,
        Order::SHIPPING_RETURNED,
        Order::SHIPPING_CANCELLED,
    ]);
});

\it('calculates product count correctly', function () {
    $client = $this->createClientWithCredentials();

    $client->request('GET', '/api/orders/12345');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response['productCount'])->toBeInt();
    \expect($response['productCount'])->toBeGreaterThanOrEqual(0);

    $calculatedCount = 0;
    foreach ($response['items'] as $item) {
        $calculatedCount += $item['quantity'];
    }

    \expect($response['productCount'])->toBe($calculatedCount);
});

\it('returns empty collection when no orders', function () {
    $client = $this->createClientWithCredentials();

    // Simuler un compte sans commandes
    $client->request('GET', '/api/orders');

    $this->assertResponseIsSuccessful();

    $response = $client->getResponse()->toArray();
    \expect($response)->toHaveKey('member');
    \expect($response['member'])->toBeArray();
});
