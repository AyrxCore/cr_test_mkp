<?php

declare(strict_types=1);

use App\Dto\Cart;
use App\Dto\CartOrder;
use App\Dto\Product;
use App\Dto\Seller;
use App\Dto\ShippingCostResult;
use App\Factory\DjustProductFactory;
use App\Factory\SellerFactory;
use App\Mapper\Cart\DjustCartMapper;
use App\Service\Djust\Cart\Transformer\DjustCartItemTransformer;
use App\Service\Djust\DjustProductService;
use App\Service\Djust\ProductFdpSyncService;
use App\Service\Shipping\ShippingCostService;

\uses()->group('UnitDjustCartMapper');

\beforeEach(function () {
    $this->sellerFactory = Mockery::mock(SellerFactory::class);
    $this->djustProductFactory = Mockery::mock(DjustProductFactory::class);
    $this->djustCartItemTransformer = Mockery::mock(DjustCartItemTransformer::class);
    $this->djustProductService = Mockery::mock(DjustProductService::class);
    $this->shippingCostService = Mockery::mock(ShippingCostService::class);

    $this->mapper = new DjustCartMapper(
        $this->sellerFactory,
        $this->djustProductFactory,
        $this->djustCartItemTransformer,
        $this->shippingCostService,
    );
});

\it('maps cart metadata correctly', function () {
    $seller = new Seller();
    $product = new Product();
    $product->setQuantity(5);
    $product->setExternalId('PRODUCT-1');

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);
    $this->djustCartItemTransformer->shouldReceive('transform')->once()->andReturn([]);
    $this->djustProductFactory->shouldReceive('create')->once()->andReturn($product);

    $djustCart = [
        'reference' => 'CART-123',
        'orderLogisticPrices' => [
            'currency' => 'EUR',
        ],
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [
                    'totalPriceWithoutTax' => 150.00,
                    'totalPriceWithTax' => 180.00,
                ],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'PRODUCT-1'],
                        'offerPriceSnapshotDto' => [],
                    ],
                ],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart)->toBeInstanceOf(Cart::class);
    \expect($cart->getId())->toBe('CART-123');
    \expect($cart->getProductCount())->toBe(5);
    \expect($cart->getTotalPrice())->toBe(150.00);
    \expect($cart->getTotalPriceWithTax())->toBe(180.00);
    \expect($cart->getCurrency())->toBe('EUR');
});

\it('handles empty cart data gracefully', function () {
    $cart = $this->mapper->mapCommercialOrderToCart([]);

    \expect($cart->getId())->toBeNull();
    \expect($cart->getProductCount())->toBe(0);
    \expect($cart->getTotalPrice())->toBe(0.0);
    \expect($cart->getTotalPriceWithTax())->toBe(0.0);
    \expect($cart->getCurrency())->toBeNull();
    \expect($cart->getCartOrders())->toBeEmpty();
});

\it('handles missing orderLogisticPrices', function () {
    $djustCart = [
        'reference' => 'CART-456',
        'productCount' => 3,
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getId())->toBe('CART-456');
    \expect($cart->getTotalPrice())->toBe(0.0);
    \expect($cart->getTotalPriceWithTax())->toBe(0.0);
    \expect($cart->getCurrency())->toBeNull();
});

\it('creates CartOrder for each orderLogistic', function () {
    $seller = new Seller();
    $seller->setId('seller-123');

    $this->sellerFactory
        ->shouldReceive('create')
        ->twice()
        ->andReturn($seller);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => ['id' => 'seller-1'],
                'orderLogisticPrices' => [
                    'totalPriceWithoutTax' => 100.00,
                    'totalPriceWithTax' => 120.00,
                ],
                'lines' => [],
            ],
            [
                'supplierSnapshot' => ['id' => 'seller-2'],
                'orderLogisticPrices' => [
                    'totalPriceWithoutTax' => 200.00,
                    'totalPriceWithTax' => 240.00,
                ],
                'lines' => [],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders())->toHaveCount(2);
    \expect($cart->getCartOrders()[0])->toBeInstanceOf(CartOrder::class);
    \expect($cart->getCartOrders()[0]->getTotalPrice())->toBe(100.00);
    \expect($cart->getCartOrders()[0]->getTotalPriceWithTax())->toBe(120.00);
    \expect($cart->getCartOrders()[1]->getTotalPrice())->toBe(200.00);
    \expect($cart->getCartOrders()[1]->getTotalPriceWithTax())->toBe(240.00);
});

\it('calls sellerFactory for each orderLogistic', function () {
    $seller = new Seller();
    $seller->setId('seller-123');

    $supplierData = ['id' => 'supplier-456', 'name' => 'Test Supplier'];

    $this->sellerFactory
        ->shouldReceive('create')
        ->once()
        ->with($supplierData)
        ->andReturn($seller);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => $supplierData,
                'orderLogisticPrices' => [],
                'lines' => [],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders()[0]->getSeller())->toBe($seller);
});

\it('handles missing orderLogistics', function () {
    $djustCart = [
        'reference' => 'CART-789',
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders())->toBeEmpty();
});

\it('handles empty lines array', function () {
    $seller = new Seller();

    $this->sellerFactory
        ->shouldReceive('create')
        ->once()
        ->andReturn($seller);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [],
                'lines' => [],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders()[0]->getProducts())->toBeEmpty();
});

\it('processes each cart line item', function () {
    $seller = new Seller();
    $product1 = new Product();
    $product2 = new Product();

    $this->sellerFactory
        ->shouldReceive('create')
        ->once()
        ->andReturn($seller);

    $lineItem1 = [
        'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-1'],
        'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 50.00],
    ];

    $lineItem2 = [
        'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-2'],
        'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 75.00],
    ];

    $supplierSnapshot = ['id' => 'seller-1'];

    $this->djustCartItemTransformer
        ->shouldReceive('transform')
        ->once()
        ->with($lineItem1, $supplierSnapshot)
        ->andReturn(['transformed' => 'data1']);

    $this->djustCartItemTransformer
        ->shouldReceive('transform')
        ->once()
        ->with($lineItem2, $supplierSnapshot)
        ->andReturn(['transformed' => 'data2']);

    $this->djustProductFactory
        ->shouldReceive('create')
        ->once()
        ->with(['transformed' => 'data1'])
        ->andReturn($product1);

    $this->djustProductFactory
        ->shouldReceive('create')
        ->once()
        ->with(['transformed' => 'data2'])
        ->andReturn($product2);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => $supplierSnapshot,
                'orderLogisticPrices' => [],
                'lines' => [$lineItem1, $lineItem2],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders()[0]->getProducts())->toHaveCount(2);
    \expect($cart->getCartOrders()[0]->getProducts()[0])->toBe($product1);
    \expect($cart->getCartOrders()[0]->getProducts()[1])->toBe($product2);
});

it('applies discount price when matching item price', function () {
    $seller = new Seller();
    $product = new Product();

    $this->sellerFactory->shouldReceive('create')->andReturn($seller);

    $transformedData = [
        'offers' => [
            [
                'offerPrices' => [
                    [
                        'priceRanges' => [
                            [
                                'price' => [],
                                'discountPrice' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->djustCartItemTransformer
        ->shouldReceive('transform')
        ->andReturn($transformedData);

    $expectedData = [
        'offers' => [
            [
                'offerPrices' => [
                    [
                        'priceRanges' => [
                            [
                                'price' => [
                                    'itemPrice' => 100.00,
                                    'unitPrice' => 100.00,
                                ],
                                'discountPrice' => [
                                    'itemPrice' => 80.00,
                                    'unitPrice' => 80.00,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->djustProductFactory
        ->shouldReceive('create')
        ->once()
        ->with($expectedData)
        ->andReturn($product);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [],
                'lines' => [
                    [
                        'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-1'],
                        'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 80.00],
                        'offerPrices' => [
                            'content' => [
                                [
                                    'priceRanges' => [
                                        [
                                            'price' => [
                                                'unitPrice' => 100.00,
                                            ],
                                            'discountPrice' => [
                                                'itemPrice' => 80.00,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapCommercialOrderToCart($djustCart);
});

\it('does not apply discount when item price does not match any range', function () {
    $seller = new Seller();
    $product = new Product();

    $this->sellerFactory->shouldReceive('create')->andReturn($seller);

    $transformedData = [
        'offers' => [[
            'offerPrices' => [[
                'priceRanges' => [[
                    'price' => [],
                    'discountPrice' => [],
                ]],
            ]],
        ]],
    ];

    $this->djustCartItemTransformer->shouldReceive('transform')->andReturn($transformedData);

    // Les données transformées ne doivent PAS être modifiées car 50 ≠ 80
    $this->djustProductFactory
        ->shouldReceive('create')
        ->once()
        ->with($transformedData)
        ->andReturn($product);

    $djustCart = [
        'orderLogistics' => [[
            'supplierSnapshot' => [],
            'orderLogisticPrices' => [],
            'lines' => [[
                'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-1'],
                'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 50.00],
                // offerPrices présent avec un discountPrice qui ne correspond pas (80 ≠ 50)
                'offerPrices' => [
                    'content' => [[
                        'priceRanges' => [[
                            'price' => ['unitPrice' => 120.00],
                            'discountPrice' => ['itemPrice' => 80.00],
                        ]],
                    ]],
                ],
            ]],
        ]],
    ];

    $this->mapper->mapCommercialOrderToCart($djustCart);
});

\it('handles null item price when checking discount', function () {
    $seller = new Seller();
    $product = new Product();

    $this->sellerFactory->shouldReceive('create')->andReturn($seller);

    $transformedData = ['data' => 'transformed'];

    $this->djustCartItemTransformer->shouldReceive('transform')->andReturn($transformedData);

    // Quand productPriceWithoutTaxes est absent, $itemPrice = null
    // → la condition if ($itemPrice !== null && ...) est fausse, données non modifiées
    $this->djustProductFactory
        ->shouldReceive('create')
        ->once()
        ->with($transformedData)
        ->andReturn($product);

    $djustCart = [
        'orderLogistics' => [[
            'supplierSnapshot' => [],
            'orderLogisticPrices' => [],
            'lines' => [[
                'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-1'],
                'offerPriceSnapshotDto' => [], // Pas de productPriceWithoutTaxes → itemPrice null
            ]],
        ]],
    ];

    $this->mapper->mapCommercialOrderToCart($djustCart);
});

\it('handles missing offerPrices content', function () {
    $seller = new Seller();
    $product = new Product();

    $this->sellerFactory->shouldReceive('create')->andReturn($seller);

    $transformedData = ['data' => 'transformed'];

    $this->djustCartItemTransformer->shouldReceive('transform')->andReturn($transformedData);

    // Quand offerPrices['content'] est absent, isset(...) est faux → données non modifiées
    $this->djustProductFactory
        ->shouldReceive('create')
        ->with($transformedData)
        ->andReturn($product);

    $djustCart = [
        'orderLogistics' => [[
            'supplierSnapshot' => [],
            'orderLogisticPrices' => [],
            'lines' => [[
                'offerInventorySnapshotDto' => ['offerInventoryExternalId' => 'INV-1'],
                'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 50.00],
                // offerPrices absent → pas de content → pas de modification
            ]],
        ]],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    \expect($cart->getCartOrders()[0]->getProducts())->toHaveCount(1);
});

// -------------------------------------------------------------------------
// subtractProductsFdpTotals
// -------------------------------------------------------------------------

\it('subtracts FDP product totals from the CartOrder prices', function () {
    $seller = new Seller();

    $realProduct = new Product();
    $realProduct->setExternalId('REAL-001')->setQuantity(1);

    $fdpProduct = new Product();
    $fdpProduct->setExternalId(ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1')->setQuantity(1);

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);

    $this->djustCartItemTransformer->shouldReceive('transform')->twice()->andReturn([]);

    $this->djustProductFactory->shouldReceive('create')
        ->twice()
        ->andReturnUsing(function () use ($realProduct, $fdpProduct) {
            static $n = 0;
            return $n++ === 0 ? $realProduct : $fdpProduct;
        });

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [
                    'totalPriceWithoutTax' => 200.00,
                    'totalPriceWithTax'    => 240.00,
                ],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'REAL-001'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 100.00],
                    ],
                    [
                        'orderLogisticLineProductDto' => ['externalId' => ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1'],
                        'offerPriceSnapshotDto'       => [
                            'productPriceWithoutTaxes' => 100.00,
                            'productPriceWithTaxes'    => 120.00,
                        ],
                        'quantity' => 1,
                    ],
                ],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);
    $cartOrder = $cart->getCartOrders()[0];

    // 200 - 100*1 = 100 HT, 240 - 120*1 = 120 TTC
    \expect($cartOrder->getTotalPrice())->toBe(100.0);
    \expect($cartOrder->getTotalPriceWithTax())->toBe(120.0);
});

\it('does not subtract when no FDP product in lines', function () {
    $seller = new Seller();
    $product = new Product();
    $product->setExternalId('REAL-001')->setQuantity(2);

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);
    $this->djustCartItemTransformer->shouldReceive('transform')->once()->andReturn([]);
    $this->djustProductFactory->shouldReceive('create')->once()->andReturn($product);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [
                    'totalPriceWithoutTax' => 150.00,
                    'totalPriceWithTax'    => 180.00,
                ],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'REAL-001'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 75.00],
                    ],
                ],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);
    $cartOrder = $cart->getCartOrders()[0];

    \expect($cartOrder->getTotalPrice())->toBe(150.0);
    \expect($cartOrder->getTotalPriceWithTax())->toBe(180.0);
});

// -------------------------------------------------------------------------
// extractMaxTaxRate
// -------------------------------------------------------------------------

\it('computes the shipping cost using the max tax rate from real products', function () {
    $seller = new Seller();
    $seller->setExternalId('EXT-SUPPLIER-1');

    $realProduct = new Product();
    $realProduct->setExternalId('REAL-001')->setQuantity(1)->setPrice(50.0);

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);
    $this->djustCartItemTransformer->shouldReceive('transform')->once()->andReturn([]);
    $this->djustProductFactory->shouldReceive('create')->once()->andReturn($realProduct);

    $shippingResult = Mockery::mock(ShippingCostResult::class);
    $shippingResult->shouldReceive('withMaxTaxRate')->once()->with(20.0)->andReturnSelf();

    $this->shippingCostService->shouldReceive('computeForPartnerDjustId')
        ->once()
        ->with('EXT-SUPPLIER-1', Mockery::any())
        ->andReturn($shippingResult);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => ['externalId' => 'EXT-SUPPLIER-1'],
                'orderLogisticPrices' => [],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'REAL-001'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 50.0],
                        'offerInventorySnapshotDto'   => [
                            'customFieldValueSnapshots' => [
                                [
                                    'customFieldSnapshotDto' => ['externalId' => 'Offer_Tax_Rate'],
                                    'typedValue' => [20.0],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapCommercialOrderToCart($djustCart);
});

\it('ignores FDP products when extracting max tax rate', function () {
    $seller = new Seller();
    $seller->setExternalId('EXT-SUPPLIER-1');

    $realProduct = new Product();
    $realProduct->setExternalId('REAL-001')->setQuantity(1)->setPrice(50.0);

    $fdpProduct = new Product();
    $fdpProduct->setExternalId(ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1')->setQuantity(1);

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);
    $this->djustCartItemTransformer->shouldReceive('transform')->twice()->andReturn([]);
    $this->djustProductFactory->shouldReceive('create')
        ->twice()
        ->andReturnUsing(function () use ($realProduct, $fdpProduct) {
            static $n = 0;
            return $n++ === 0 ? $realProduct : $fdpProduct;
        });

    $shippingResult = Mockery::mock(ShippingCostResult::class);
    // FDP line has rate 50, real product has rate 10 → max should be 10 (FDP ignored)
    $shippingResult->shouldReceive('withMaxTaxRate')->once()->with(10.0)->andReturnSelf();

    $this->shippingCostService->shouldReceive('computeForPartnerDjustId')
        ->once()
        ->andReturn($shippingResult);

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => ['externalId' => 'EXT-SUPPLIER-1'],
                'orderLogisticPrices' => [],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'REAL-001'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 50.0],
                        'offerInventorySnapshotDto'   => [
                            'customFieldValueSnapshots' => [
                                [
                                    'customFieldSnapshotDto' => ['externalId' => 'Offer_Tax_Rate'],
                                    'typedValue' => [10.0],
                                ],
                            ],
                        ],
                    ],
                    [
                        'orderLogisticLineProductDto' => ['externalId' => ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 1.0, 'productPriceWithTaxes' => 1.0],
                        'quantity'                    => 1,
                        'offerInventorySnapshotDto'   => [
                            'customFieldValueSnapshots' => [
                                [
                                    'customFieldSnapshotDto' => ['externalId' => 'Offer_Tax_Rate'],
                                    'typedValue' => [50.0],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapCommercialOrderToCart($djustCart);
});

// -------------------------------------------------------------------------
// productCount — FDP exclusion
// -------------------------------------------------------------------------

\it('excludes FDP products from the productCount', function () {
    $seller = new Seller();

    $realProduct = new Product();
    $realProduct->setExternalId('REAL-001')->setQuantity(3);

    $fdpProduct = new Product();
    $fdpProduct->setExternalId(ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1')->setQuantity(999);

    $this->sellerFactory->shouldReceive('create')->once()->andReturn($seller);
    $this->djustCartItemTransformer->shouldReceive('transform')->twice()->andReturn([]);
    $this->djustProductFactory->shouldReceive('create')
        ->twice()
        ->andReturnUsing(function () use ($realProduct, $fdpProduct) {
            static $n = 0;
            return $n++ === 0 ? $realProduct : $fdpProduct;
        });

    $djustCart = [
        'orderLogistics' => [
            [
                'supplierSnapshot' => [],
                'orderLogisticPrices' => [],
                'lines' => [
                    [
                        'orderLogisticLineProductDto' => ['externalId' => 'REAL-001'],
                        'offerPriceSnapshotDto'       => [],
                    ],
                    [
                        'orderLogisticLineProductDto' => ['externalId' => ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1'],
                        'offerPriceSnapshotDto'       => ['productPriceWithoutTaxes' => 0, 'productPriceWithTaxes' => 0],
                        'quantity'                    => 1,
                    ],
                ],
            ],
        ],
    ];

    $cart = $this->mapper->mapCommercialOrderToCart($djustCart);

    // Seul le produit réel (qty=3) doit compter
    \expect($cart->getProductCount())->toBe(3);
});

