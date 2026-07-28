<?php

declare(strict_types=1);

use App\Entity\Account;
use App\Entity\CartSavings;
use App\Repository\AccountRepository;
use App\Repository\CartSavingsRepository;
use App\Service\CartSavingsService;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\DjustOrderService;
use App\Service\Djust\DjustProductService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

\uses()->group('UnitCartSavingsService', 'cart', 'analytics');

\beforeEach(function () {
    $this->repository = Mockery::mock(CartSavingsRepository::class);
    $this->accountRepository = Mockery::mock(AccountRepository::class);
    $this->orderService = Mockery::mock(DjustOrderService::class);
    $this->djustProductService = Mockery::mock(DjustProductService::class);
    $this->djustDataExtractor = Mockery::mock(DjustDataExtractor::class);
    $this->entityManager = Mockery::mock(EntityManagerInterface::class);
    $this->logger = Mockery::mock(LoggerInterface::class)->shouldIgnoreMissing();

    // Par défaut : l'API catalogue lève une exception (session non disponible en unit test)
    $this->djustProductService->shouldReceive('getProductOffers')
        ->andThrow(new \RuntimeException('No session in unit test context'))
        ->byDefault();

    // Par défaut : réplique le vrai comportement d'extraction du sellerId depuis supplierSnapshot.id
    $this->djustDataExtractor->shouldReceive('extractSellerId')
        ->andReturnUsing(static function (array $orderLogistic) {
            $rawSellerId = $orderLogistic['supplierSnapshot']['id'] ?? null;

            if ($rawSellerId === null) {
                return 0;
            }
            if (\is_int($rawSellerId)) {
                return $rawSellerId;
            }
            if (\is_string($rawSellerId) && \preg_match('/\d+/', $rawSellerId, $matches) === 1) {
                return (int) $matches[0];
            }

            return 0;
        })
        ->byDefault();

    $this->service = new CartSavingsService(
        $this->repository,
        $this->accountRepository,
        $this->orderService,
        $this->djustProductService,
        $this->djustDataExtractor,
        $this->entityManager,
        $this->logger,
    );

    $this->account = Mockery::mock(Account::class);
    $this->account->shouldReceive('getId')->andReturn(Uuid::v4());
    // L'account repository retourne l'account mocké (simulant le rechargement Doctrine)
    $this->accountRepository->shouldReceive('find')->andReturn($this->account);

    $this->makeOrderLogistic = static function (string $supplierId = 'supplier-1', float $itemPrice = 50.0, int $qty = 2, string $status = 'CONFIRMED'): array {
        return [
            'status' => $status,
            'supplierSnapshot' => ['id' => $supplierId],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => $itemPrice * $qty],
            'lines' => [
                [
                    'quantity' => $qty,
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => $itemPrice,
                        'totalPriceWithoutTaxes' => $itemPrice * $qty,
                    ],
                ],
            ],
        ];
    };

    $makeOrderLogistic = $this->makeOrderLogistic;
    $this->makeOrder = static function (string $id = '0000012345', string $reference = 'REF-2024-001', ?array $orderLogistics = null) use ($makeOrderLogistic): array {
        return [
            'id' => $id,
            'reference' => $reference,
            'orderLogistics' => $orderLogistics ?? [$makeOrderLogistic()],
        ];
    };
});

\it('creates CartSavings from most recent buyer order', function () {
    $order = ($this->makeOrder)();

    $this->orderService->shouldReceive('getMostRecentBuyerOrder')
        ->once()
        ->andReturn($order);

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once();
    $this->entityManager->shouldReceive('flush')->once();

    $count = $this->service->createFromMostRecentBuyerOrder($this->account);

    \expect($count)->toBe(1);
});

\it('returns 0 when no recent buyer order is found', function () {
    $this->orderService->shouldReceive('getMostRecentBuyerOrder')
        ->once()
        ->andReturn(null);

    $this->logger->shouldReceive('warning')->once();

    $count = $this->service->createFromMostRecentBuyerOrder($this->account);

    \expect($count)->toBe(0);
});

\it('returns 0 when Djust order has no id', function () {
    $this->logger->shouldReceive('warning')->once();
    $this->repository->shouldNotReceive('save');
    $this->entityManager->shouldNotReceive('flush');

    $count = $this->service->createFromDjustOrder(['reference' => 'REF-NO-ID', 'orderLogistics' => []], $this->account);

    \expect($count)->toBe(0);
});

\it('creates one CartSavings per orderLogistic', function () {
    $order = ($this->makeOrder)('0000012345', 'REF-MULTI', [
        ($this->makeOrderLogistic)('supplier-1'),
        ($this->makeOrderLogistic)('supplier-2'),
    ]);

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->twice();
    $this->entityManager->shouldReceive('flush')->once();

    $count = $this->service->createFromDjustOrder($order, $this->account);

    \expect($count)->toBe(2);
});

\it('updates existing CartSavings when record already exists', function () {
    $existing = (new CartSavings())
        ->setOrderId('0000012345')
        ->setOrderState('PENDING');

    $order = ($this->makeOrder)();

    $this->repository->shouldReceive('findOneBy')->andReturn($existing);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) {
        return $s->getOrderState() === 'CONFIRMED' && $s->getOrderId() === '0000012345';
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $count = $this->service->createFromDjustOrder($order, $this->account);

    \expect($count)->toBe(1);
    \expect($existing->getOrderState())->toBe('CONFIRMED');
});

\it('calculates amounts correctly from line prices', function () {
    $order = ($this->makeOrder)('0000099999', 'REF-CALC', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 80.0],
            'lines' => [
                [
                    'quantity' => 2,
                    'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 50.0],
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => 40.0,
                        'totalPriceWithoutTaxes' => 80.0,
                    ],
                ],
            ],
        ],
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // prix_reference = 50.0 * 2 = 100.0 → 10000 centimes
    // prix_payé = 80.0 → 8000 centimes
    // économies = 10000 - 8000 = 2000 centimes
    \expect($saved->getItemsTotalBeforeSavings())->toBe(10000);
    \expect($saved->getItemsTotal())->toBe(8000);
    \expect($saved->getAmount())->toBe(2000);
    \expect($saved->getOrderTotal())->toBe(8000); // depuis orderLogisticPrices
});

\it('excludes FDP lines from metrics', function () {
    $order = ($this->makeOrder)('0000088888', 'REF-FDP', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 50.0],
            'lines' => [
                [
                    'quantity' => 1,
                    'orderLogisticLinePriceDto' => ['itemPriceWithoutTaxes' => 50.0, 'totalPriceWithoutTaxes' => 50.0],
                    'orderLogisticLineProductDto' => ['externalId' => 'non-fdp'],
                ],
                [
                    'quantity' => 1,
                    'orderLogisticLinePriceDto' => ['itemPriceWithoutTaxes' => 5.0, 'totalPriceWithoutTaxes' => 5.0],
                    'orderLogisticLineProductDto' => ['externalId' => 'PRODUCT_FDP_abc'],
                ],
            ],
        ],
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // Seule la ligne non-FDP est prise en compte : 50.0 → 5000 centimes
    \expect($saved->getItemsTotal())->toBe(5000);
    \expect($saved->getItemsTotalBeforeSavings())->toBe(5000);
});

\it('stores the orderLogistic status as orderState', function () {
    $order = ($this->makeOrder)('0000077777', 'REF-STATUS', [
        ($this->makeOrderLogistic)('supplier-1', 50.0, 1, 'SHIPPED'),
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    \expect($saved->getOrderState())->toBe('SHIPPED');
});

\it('uses order id as orderId on CartSavings', function () {
    $order = ($this->makeOrder)('0000012345', 'CART-REF');

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    \expect($saved->getOrderId())->toBe('0000012345');
    \expect($saved->getCartId())->toBe('CART-REF');
});

\it('uses the orderLogistic id as sellerOrderId, distinct from the parent orderId (MKP-1557)', function () {
    // Sur Djust, chaque orderLogistic (commande par vendeur) a son propre id,
    // différent de l'id de la commande parente affiché par ailleurs dans le BO Djust.
    $order = ($this->makeOrder)('0000625780', 'CART-MULTI-SELLER', [
        [...($this->makeOrderLogistic)('supplier-1'), 'id' => '0004078562'],
        [...($this->makeOrderLogistic)('supplier-2'), 'id' => '0004078566'],
    ]);

    $saved = [];

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->twice()->with(Mockery::on(static function (CartSavings $s) use (&$saved) {
        $saved[] = $s;

        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    \expect($saved)->toHaveCount(2);
    foreach ($saved as $cartSaving) {
        // orderId reste celui de la commande parente (nécessaire pour le sync par la suite)
        \expect($cartSaving->getOrderId())->toBe('0000625780');
    }
    \expect($saved[0]->getSellerOrderId())->toBe('0004078562');
    \expect($saved[1]->getSellerOrderId())->toBe('0004078566');
});

\it('uses OFFER_PUBLIC_PRICE from offerCustomFieldSnapshotDtos as reference price for savings', function () {
    $order = ($this->makeOrder)('0000099998', 'REF-PUBLIC-PRICE', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 4.0],
            'lines' => [
                [
                    'quantity' => 2,
                    'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 2.0],
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => 2.0,
                        'totalPriceWithoutTaxes' => 4.0,
                    ],
                    'offerCustomFieldSnapshotDtos' => [
                        [
                            'offerCustomFieldSnapshotDto' => ['externalId' => 'OFFER_PUBLIC_PRICE'],
                            'value' => '20',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // prix_reference (OFFER_PUBLIC_PRICE) = 20.0 * 2 = 40.0 → 4000 centimes
    // prix_payé = 4.0 → 400 centimes
    // économies = 4000 - 400 = 3600 centimes
    \expect($saved->getItemsTotalBeforeSavings())->toBe(4000);
    \expect($saved->getItemsTotal())->toBe(400);
    \expect($saved->getAmount())->toBe(3600);
});

\it('uses OFFER_PUBLIC_PRICE from offerInventorySnapshotDto as fallback reference price', function () {
    $order = ($this->makeOrder)('0000099997', 'REF-INVENTORY-PRICE', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 10.0],
            'lines' => [
                [
                    'quantity' => 1,
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => 10.0,
                        'totalPriceWithoutTaxes' => 10.0,
                    ],
                    'offerInventorySnapshotDto' => [
                        'customFieldValueSnapshots' => [
                            [
                                'customFieldSnapshotDto' => ['externalId' => 'OFFER_PUBLIC_PRICE'],
                                'value' => '30',
                                'typedValue' => '30',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // prix_reference (OFFER_PUBLIC_PRICE via inventory) = 30.0 * 1 = 30.0 → 3000 centimes
    // prix_payé = 10.0 → 1000 centimes
    // économies = 3000 - 1000 = 2000 centimes
    \expect($saved->getItemsTotalBeforeSavings())->toBe(3000);
    \expect($saved->getItemsTotal())->toBe(1000);
    \expect($saved->getAmount())->toBe(2000);
});

\it('falls back to productPriceWithoutTaxes when OFFER_PUBLIC_PRICE is absent', function () {
    $order = ($this->makeOrder)('0000099996', 'REF-FALLBACK', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 80.0],
            'lines' => [
                [
                    'quantity' => 2,
                    'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 50.0],
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => 40.0,
                        'totalPriceWithoutTaxes' => 80.0,
                    ],
                    // pas de offerCustomFieldSnapshotDtos ni offerInventorySnapshotDto
                ],
            ],
        ],
    ]);

    $saved = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;
        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // fallback productPriceWithoutTaxes = 50.0 * 2 = 100 → 10000 centimes
    \expect($saved->getItemsTotalBeforeSavings())->toBe(10000);
    \expect($saved->getItemsTotal())->toBe(8000);
    \expect($saved->getAmount())->toBe(2000);
});

\it('uses totalItemPrice (not totalPriceWithoutTaxes) so items_total excludes per-line shipping', function () {
    // Simule une ligne Djust avec livraison incluse dans totalPriceWithoutTaxes
    // totalItemPrice = 10.0 (produits seuls), totalPriceWithoutTaxes = 12.0 (produits + livraison)
    $order = ($this->makeOrder)('0000012345', 'REF-SHIPPING', [
        [
            'status' => 'CONFIRMED',
            'supplierSnapshot' => ['id' => 'supplier-1'],
            'orderLogisticPrices' => ['totalPriceWithoutTax' => 12.0],
            'lines' => [
                [
                    'quantity' => 2,
                    'orderLogisticLinePriceDto' => [
                        'itemPriceWithoutTaxes' => 5.0,
                        'totalItemPrice' => 10.0,           // produits seuls (5.0 × 2)
                        'totalPriceWithoutTaxes' => 12.0,   // produits + livraison ligne
                    ],
                    'offerPriceSnapshotDto' => ['productPriceWithoutTaxes' => 5.0],
                    'offerCustomFieldSnapshotDtos' => [],
                    'offerInventorySnapshotDto' => ['customFieldValueSnapshots' => []],
                    'orderLogisticLineProductDto' => ['externalId' => 'PROD-1'],
                ],
            ],
        ],
    ]);

    $saved = null;
    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$saved) {
        $saved = $s;

        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    // items_total = totalItemPrice = 10.0€ = 1000 centimes (pas 12.0€ = 1200)
    \expect($saved->getItemsTotal())->toBe(1000);
    // order_total = totalPriceWithoutTax = 12.0€ = 1200 centimes (inclut livraison)
    \expect($saved->getOrderTotal())->toBe(1200);
});

\it('does not flush when no orderLogistics', function () {
    $order = ($this->makeOrder)('0000012345', 'REF-EMPTY', []);

    $this->repository->shouldNotReceive('findOneBy');
    $this->repository->shouldNotReceive('save');
    $this->entityManager->shouldNotReceive('flush');

    $count = $this->service->createFromDjustOrder($order, $this->account);

    \expect($count)->toBe(0);
});

\it('stores orderId with more than 4 chars when creating CartSavings from Djust order', function () {
    $order = ($this->makeOrder)('0000012345', 'REF-PAD', [($this->makeOrderLogistic)('supplier-1')]);
    $savedSaving = null;

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once()->with(Mockery::on(function (CartSavings $s) use (&$savedSaving) {
        $savedSaving = $s;

        return true;
    }));
    $this->entityManager->shouldReceive('flush')->once();

    $this->service->createFromDjustOrder($order, $this->account);

    \expect($savedSaving)->not->toBeNull();
    \expect(\strlen($savedSaving->getOrderId()))->toBeGreaterThan(4);
});

// --- createAfterPayment ---

\it('createAfterPayment logs warning and returns early when account is null', function () {
    $this->logger->shouldReceive('warning')
        ->once()
        ->with('CartSavings not created: no account in session.', Mockery::any());

    $this->orderService->shouldNotReceive('getMostRecentBuyerOrder');

    $this->service->createAfterPayment('CART-REF-001', null);
});

\it('createAfterPayment logs info when CartSavings are created successfully', function () {
    $order = ($this->makeOrder)();

    $this->orderService->shouldReceive('getMostRecentBuyerOrder')
        ->once()
        ->andReturn($order);

    $this->repository->shouldReceive('findOneBy')->andReturn(null);
    $this->repository->shouldReceive('save')->once();
    $this->entityManager->shouldReceive('flush')->once();

    $this->logger->shouldReceive('info')
        ->once()
        ->with('CartSavings created after payment.', Mockery::any());

    $this->service->createAfterPayment('CART-REF-002', $this->account);
});

\it('createAfterPayment logs warning when no order found after payment', function () {
    $this->orderService->shouldReceive('getMostRecentBuyerOrder')
        ->once()
        ->andReturn(null);

    $this->logger->shouldReceive('warning')
        ->with('Unable to create CartSavings: no recent order found for buyer.', Mockery::any())
        ->once();

    $this->logger->shouldReceive('warning')
        ->with('CartSavings not created after payment: no order found.', Mockery::any())
        ->once();

    $this->service->createAfterPayment('CART-REF-003', $this->account);
});

\it('createAfterPayment logs warning when buyer order fetch fails', function () {
    $this->orderService->shouldReceive('getMostRecentBuyerOrder')
        ->once()
        ->andThrow(new \RuntimeException('API timeout'));

    $this->logger->shouldReceive('warning')
        ->with('CartSavings: buyer order fetch failed.', Mockery::any())
        ->once();

    $this->logger->shouldReceive('warning')
        ->with('CartSavings not created after payment: no order found.', Mockery::any())
        ->once();

    $this->service->createAfterPayment('CART-REF-004', $this->account);
});
