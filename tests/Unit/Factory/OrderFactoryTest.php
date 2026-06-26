<?php

declare(strict_types=1);

use App\Dto\Order;
use App\Factory\OrderFactory;

\uses()->group('UnitOrderFactory', 'UnitOrder');

\beforeEach(function () {
    $cache = Mockery::mock(\Psr\Cache\CacheItemPoolInterface::class);
    $this->factory = new OrderFactory($cache);
});

\afterEach(function () {
    Mockery::close();
});

\it('creates order with basic info', function () {
    $data = [
        'id' => '0000012345',
        'reference' => 'REF-123',
        'orderLogistics' => [['status' => 'CONFIRMED']],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Order::class);
    \expect($result->getId())->toBe(12345);
    \expect($result->getOrderNumber())->toBe('REF-123');
});

\it('sets prices from orderLogisticPrices', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLinePriceDto' => ['totalPriceWithoutTaxes' => 100.00],
                        'orderLogisticLineProductDto' => ['externalId' => 'PRODUCT-1'],
                    ],
                    [
                        'quantity' => 1,
                        'orderLogisticLinePriceDto' => ['totalPriceWithoutTaxes' => 15.00],
                        'orderLogisticLineProductDto' => ['externalId' => 'PRODUCT_FDP_SHIPPING'],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [
            'totalPriceWithTax' => 120.50,
        ],
    ];

    $result = $this->factory->create($data);

    \expect($result->getTotal())->toBe(120.50);
    \expect($result->getTotalExcludingTaxes())->toBe(100.00);
    \expect($result->getShipmentAmount())->toBe(15.00);
});

\it('maps djust status to shipping status', function () {
    $data = [
        'orderLogistics' => [['status' => 'CONFIRMED']],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingState())->toBe(Order::SHIPPING_PREPARATION);
});

\it('maps pending status correctly', function () {
    $data = [
        'orderLogistics' => [['status' => 'PENDING_SUPPLIER_CONFIRMATION']],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingState())->toBe(Order::SHIPPING_PENDING);
});

\it('maps shipped status correctly', function () {
    $data = [
        'orderLogistics' => [['status' => 'SHIPPED']],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingState())->toBe(Order::SHIPPING_SHIPPED);
});

\it('maps cancelled status correctly', function () {
    $data = [
        'orderLogistics' => [['status' => 'CANCELED']],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingState())->toBe(Order::SHIPPING_CANCELLED);
});

\it('formats shipping address', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'shippingAddressSnapshot' => [
                    'fullName' => 'John Doe',
                    'address' => '123 Main St',
                    'additionalAddress' => 'Apt 4',
                    'zipcode' => '75001',
                    'city' => 'Paris',
                    'country' => 'France',
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingAddress())->toContain('John Doe');
    \expect($result->getShippingAddress())->toContain('123 Main St');
    \expect($result->getShippingAddress())->toContain('75001 Paris');
});

\it('formats billing address', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'billingAddressSnapshot' => [
                    'fullName' => 'Jane Doe',
                    'address' => '456 Oak Ave',
                    'zipcode' => '75002',
                    'city' => 'Paris',
                    'country' => 'France',
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getBillingAddress())->toContain('Jane Doe');
    \expect($result->getBillingAddress())->toContain('456 Oak Ave');
});

\it('sets tracking url', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'SHIPPED',
                'shippingTrackingUrl' => 'https://tracking.example.com/123',
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getShippingTrackingUrl())->toBe('https://tracking.example.com/123');
});

\it('sets dates correctly', function () {
    $data = [
        'orderLogistics' => [['status' => 'CONFIRMED']],
        'orderLogisticPrices' => [],
        'createdAt' => '2024-01-15T10:00:00+00:00',
        'updatedAt' => '2024-01-16T12:00:00+00:00',
        'validatedAt' => '2024-01-16T11:00:00+00:00',
    ];

    $result = $this->factory->create($data);

    \expect($result->getCreatedAt())->toBeInstanceOf(\DateTimeInterface::class);
    \expect($result->getUpdatedAt())->toBeInstanceOf(\DateTimeInterface::class);
    \expect($result->getConfirmedAt())->toBeInstanceOf(\DateTimeInterface::class);
});

\it('calculates product count from items', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'lines' => [
                    ['quantity' => 2],
                    ['quantity' => 3],
                ],
            ],
            [
                'status' => 'CONFIRMED',
                'lines' => [
                    ['quantity' => 1],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getProductCount())->toBe(6);
});

\it('maps order items with product details', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Test Seller'],
                'lines' => [
                    [
                        'quantity' => 2,
                        'orderLogisticLineProductDto' => [
                            'djustProductUuid' => 'prod-123',
                            'externalId' => 'ext-123',
                            'name' => 'Test Product',
                            'mainImageUrl' => 'https://example.com/image.jpg',
                        ],
                        'orderLogisticLineProductVariantDto' => [
                            'sku' => 'SKU-123',
                            'externalReference' => 'REF-123',
                        ],
                        'orderLogisticLinePriceDto' => [
                            'itemPriceWithoutTaxes' => 50.00,
                            'totalPriceWithoutTaxes' => 100.00,
                        ],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);
    $items = $result->getItems();

    \expect($items)->toHaveCount(1);
    \expect($items[0]['quantity'])->toBe(2);
    \expect($items[0]['unit_price'])->toBe(50.00);
    \expect($items[0]['total_excluding_taxes'])->toBe(10000);
    \expect($items[0]['variant']['sku'])->toBe('SKU-123');
    \expect($items[0]['variant']['product']['name']['default'])->toBe('Test Product');
    \expect($items[0]['variant']['product']['seller']['name'])->toBe('Test Seller');
});

\it('extracts invoice url from custom fields', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'COMMERCIAL_ORDER_PARTNER_INVOICE'],
                        'value' => 'https://example.com/invoice.pdf',
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getInvoiceUrl())->toBe('https://example.com/invoice.pdf');
});

\it('handles missing invoice url gracefully', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'customFieldValues' => [],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getInvoiceUrl())->toBeNull();
});

\it('handles empty order logistics', function () {
    $data = [
        'orderLogistics' => [],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Order::class);
    \expect($result->getProductCount())->toBe(0);
    \expect($result->getItems())->toBe([]);
});

\it('creates orderInvoiceLinks for multiple sub-references', function () {
    $data = [
        'reference' => '123-456-789',
        'orderLogistics' => [
            [
                'reference' => '123-456-789-1',
                'status' => 'CONFIRMED',
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'COMMERCIAL_ORDER_PARTNER_INVOICE'],
                        'value' => 'https://invoice1.pdf',
                    ],
                ],
            ],
            [
                'reference' => '123-456-789-2',
                'status' => 'SHIPPED',
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'COMMERCIAL_ORDER_PARTNER_INVOICE'],
                        'value' => 'https://invoice2.pdf',
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getOrderInvoiceLinks())->toHaveCount(2);
    \expect($result->getOrderInvoiceLinks()[0]['reference'])->toBe('123-456-789-1');
    \expect($result->getOrderInvoiceLinks()[0]['invoiceUrl'])->toBe('https://invoice1.pdf');
    \expect($result->getOrderInvoiceLinks()[1]['reference'])->toBe('123-456-789-2');
    \expect($result->getOrderInvoiceLinks()[1]['invoiceUrl'])->toBe('https://invoice2.pdf');
});

\it('creates orderPartners for multiple sub-references', function () {
    $data = [
        'reference' => '123-456-789',
        'orderLogistics' => [
            [
                'reference' => '123-456-789-1',
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Partner A'],
                'shippingTrackingUrl' => 'https://track1.com',
                'lines' => [
                    [
                        'quantity' => 2,
                        'orderLogisticLineProductDto' => ['name' => 'Product 1'],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU1'],
                        'orderLogisticLinePriceDto' => ['itemPriceWithoutTaxes' => 10, 'totalPriceWithoutTaxes' => 20],
                    ],
                ],
            ],
            [
                'reference' => '123-456-789-2',
                'status' => 'SHIPPED',
                'supplierSnapshot' => ['name' => 'Partner B'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => ['name' => 'Product 2'],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU2'],
                        'orderLogisticLinePriceDto' => ['itemPriceWithoutTaxes' => 15, 'totalPriceWithoutTaxes' => 15],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getOrderPartners())->toHaveCount(2);
    \expect($result->getOrderPartners()[0]['reference'])->toBe('123-456-789-1');
    \expect($result->getOrderPartners()[0]['partnerName'])->toBe('Partner A');
    \expect($result->getOrderPartners()[0]['shippingState'])->toBe(Order::SHIPPING_PREPARATION);
    \expect($result->getOrderPartners()[0]['shippingTrackingUrl'])->toBe('https://track1.com');
    \expect($result->getOrderPartners()[0]['items'])->toHaveCount(1);
    \expect($result->getOrderPartners()[1]['reference'])->toBe('123-456-789-2');
    \expect($result->getOrderPartners()[1]['partnerName'])->toBe('Partner B');
    \expect($result->getOrderPartners()[1]['shippingState'])->toBe(Order::SHIPPING_SHIPPED);
});

\it('does not create orderPartners for single logistics', function () {
    $data = [
        'reference' => '123-456-789',
        'orderLogistics' => [
            [
                'reference' => '123-456-789',
                'status' => 'CONFIRMED',
                'lines' => [],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getOrderPartners())->toBeEmpty();
});

\it('does not create orderPartners when references do not match pattern', function () {
    $data = [
        'reference' => '123-456-789',
        'orderLogistics' => [
            [
                'reference' => '123-456-789',
                'status' => 'CONFIRMED',
                'lines' => [],
            ],
            [
                'reference' => '123-456-790',
                'status' => 'CONFIRMED',
                'lines' => [],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);

    \expect($result->getOrderPartners())->toBeEmpty();
});

\it('extracts eco tax from product attributes', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Test Seller'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => [
                            'name' => 'Product with eco-tax',
                            'productAttributeValues' => [
                                [
                                    'attributeExternalId' => 'PRODUCT_ECOTAXE',
                                    'attributeValue' => '0.74',
                                ],
                            ],
                        ],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU-123'],
                        'orderLogisticLinePriceDto' => [
                            'itemPriceWithoutTaxes' => 100.00,
                            'totalPriceWithoutTaxes' => 100.00,
                        ],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);
    $items = $result->getItems();

    \expect($items[0]['eco_tax'])->toBe(0.74);
});

\it('returns null when eco tax attribute is missing', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Test Seller'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => [
                            'name' => 'Product without eco-tax',
                            'productAttributeValues' => [],
                        ],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU-123'],
                        'orderLogisticLinePriceDto' => [
                            'itemPriceWithoutTaxes' => 100.00,
                            'totalPriceWithoutTaxes' => 100.00,
                        ],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);
    $items = $result->getItems();

    \expect($items[0]['eco_tax'])->toBeNull();
});

\it('returns null when eco tax value is empty', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Test Seller'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => [
                            'name' => 'Product with empty eco-tax',
                            'productAttributeValues' => [
                                [
                                    'attributeExternalId' => 'PRODUCT_ECOTAXE',
                                    'attributeValue' => '',
                                ],
                            ],
                        ],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU-123'],
                        'orderLogisticLinePriceDto' => [
                            'itemPriceWithoutTaxes' => 100.00,
                            'totalPriceWithoutTaxes' => 100.00,
                        ],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);
    $items = $result->getItems();

    \expect($items[0]['eco_tax'])->toBeNull();
});

\it('converts eco tax string to float', function () {
    $data = [
        'orderLogistics' => [
            [
                'status' => 'CONFIRMED',
                'supplierSnapshot' => ['name' => 'Test Seller'],
                'lines' => [
                    [
                        'quantity' => 1,
                        'orderLogisticLineProductDto' => [
                            'name' => 'Product with decimal eco-tax',
                            'productAttributeValues' => [
                                [
                                    'attributeExternalId' => 'PRODUCT_ECOTAXE',
                                    'attributeValue' => '12.50',
                                ],
                            ],
                        ],
                        'orderLogisticLineProductVariantDto' => ['sku' => 'SKU-123'],
                        'orderLogisticLinePriceDto' => [
                            'itemPriceWithoutTaxes' => 100.00,
                            'totalPriceWithoutTaxes' => 100.00,
                        ],
                    ],
                ],
            ],
        ],
        'orderLogisticPrices' => [],
    ];

    $result = $this->factory->create($data);
    $items = $result->getItems();

    \expect($items[0]['eco_tax'])->toBe(12.50);
    \expect($items[0]['eco_tax'])->toBeFloat();
});

