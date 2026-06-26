<?php

declare(strict_types=1);

use App\Dto\Order;
use App\Factory\OrderFactory;

\uses()->group('IntegrationOrderFactory');

\it('retrieves OrderFactory from container', function () {
    $factory = $this->container->get(OrderFactory::class);

    \expect($factory)->toBeInstanceOf(OrderFactory::class);
});

\it('creates Order from Djust data with real cache', function () {
    $factory = $this->container->get(OrderFactory::class);

    $djustData = [
        'id' => '0000012345',
        'reference' => 'REF-TEST',
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'lines' => [],
            ],
        ],
        'orderLogisticPrices' => [
            'totalPriceWithTax' => 100.00,
            'totalPriceWithoutTax' => 83.33,
            'totalShippingFeesWithoutTax' => 10.00,
        ],
    ];

    $result = $factory->create($djustData);

    \expect($result)->toBeInstanceOf(Order::class)
        ->and($result->getId())->toBe(12345)
        ->and($result->getOrderNumber())->toBe('REF-TEST')
        ->and($result->getShippingState())->toBe(Order::SHIPPING_PREPARATION);
});

\it('creates multiple orders with collection method', function () {
    $factory = $this->container->get(OrderFactory::class);

    $djustData = [
        [
            'id' => '0000001',
            'reference' => 'REF-001',
            'orderLogistics' => [['status' => 'CONFIRMED']],
            'orderLogisticPrices' => [],
        ],
        [
            'id' => '0000002',
            'reference' => 'REF-002',
            'orderLogistics' => [['status' => 'SHIPPED']],
            'orderLogisticPrices' => [],
        ],
    ];

    $result = $factory->createAndAddToCollection($djustData);

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Order::class)
        ->and($result[1])->toBeInstanceOf(Order::class);
});

\it('extends AbstractFactory with cache dependency', function () {
    $factory = $this->container->get(OrderFactory::class);

    \expect($factory)->toBeInstanceOf(\App\Factory\AbstractFactory::class);
});

\it('handles complex order with multiple logistics', function () {
    $factory = $this->container->get(OrderFactory::class);

    $djustData = [
        'id' => '0000999',
        'reference' => 'MULTI-REF',
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Seller 1'],
                'lines' => [
                    [
                        'quantity' => 2,
                        'orderLogisticLineProductDto' => ['name' => 'Product 1'],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU1'],
                        'orderLogisticLinePriceDto' => ['totalPriceWithoutTaxes' => 50],
                    ],
                ],
            ],
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Seller 2'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => ['name' => 'Product 2'],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU2'],
                        'orderLogisticLinePriceDto' => ['totalPriceWithoutTaxes' => 25],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $factory->create($djustData);

    \expect($result->getProductCount())->toBe(3)
        ->and($result->getItems())->toHaveCount(2);
});

\it('maps all Djust statuses to shipping states correctly', function () {
    $factory = $this->container->get(OrderFactory::class);

    $statuses = [
        'DRAFT_ORDER' => Order::SHIPPING_PENDING,
        'PENDING_SUPPLIER_CONFIRMATION' => Order::SHIPPING_PENDING,
        'CONFIRMED' => Order::SHIPPING_PREPARATION,
        'SHIPPED' => Order::SHIPPING_SHIPPED,
        'CANCELED' => Order::SHIPPING_CANCELLED,
        'REFUSED' => Order::SHIPPING_CANCELLED,
    ];

    foreach ($statuses as $djustStatus => $expectedShippingState) {
        $data = [
            'id' => '0000001',
            'orderLogistics' => [['status' => $djustStatus]],
            'orderLogisticPrices' => [],
        ];

        $order = $factory->create($data);

        \expect($order->getShippingState())->toBe($expectedShippingState);
    }
});

\it('formats addresses with real data', function () {
    $factory = $this->container->get(OrderFactory::class);

    $data = [
        'id' => '0000001',
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'shippingAddressSnapshot' => [
                    'fullName' => 'Test User',
                    'address' => '123 Test St',
                    'zipcode' => '75001',
                    'city' => 'Paris',
                    'country' => 'France',
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $order = $factory->create($data);

    \expect($order->getShippingAddress())->toContain('Test User')
        ->and($order->getShippingAddress())->toContain('Paris');
});

\it('extracts invoice URL from custom fields', function () {
    $factory = $this->container->get(OrderFactory::class);

    $data = [
        'id' => '0000001',
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'COMMERCIAL_ORDER_PARTNER_INVOICE'],
                        'value' => 'https://invoices.example.com/123.pdf',
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $order = $factory->create($data);

    \expect($order->getInvoiceUrl())->toBe('https://invoices.example.com/123.pdf');
});
