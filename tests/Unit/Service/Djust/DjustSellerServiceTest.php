<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\DjustSellerService;
use App\Service\Djust\Search\DjustSearchService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Service\AccordCadre\AccordCadreService;

\uses()->group('UnitDjustSellerService');

\beforeEach(function () {
    $this->accountId = 'ACC123';
    $this->channelCode = 'TEST_CHANNEL';
    $this->cacheKey = DjustSellerService::SELLERS_CACHE_KEY.'_'.$this->channelCode;

    $this->cache = Mockery::mock(CacheInterface::class);
    $this->djustSearchService = Mockery::mock(DjustSearchService::class);
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->accordCadreService = Mockery::mock(AccordCadreService::class);
    
    $this->channelContext = Mockery::mock(ChannelContext::class);
    $channel = Mockery::mock();
    $channel->shouldReceive('getCode')->andReturn($this->channelCode);
    $this->channelContext->shouldReceive('getChannel')->andReturn($channel);

    $this->service = new DjustSellerService(
        $this->cache,
        $this->djustSearchService,
        $this->httpClient,
        $this->accordCadreService,
        $this->channelContext,
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('returns all sellers from cache when cache is hit', function () {
    $cachedSellers = [
        ['id' => 'SELLER-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'name' => 'Seller Two'],
        ['id' => 'SELLER-3', 'name' => 'Seller Three'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturn($cachedSellers);

    $result = $this->service->getAllSellers($this->accountId);

    \expect($result)->toBe($cachedSellers)
        ->and($result)->toHaveCount(3);
});

\it('fetches all sellers from API when cache is miss', function () {
    $page1Data = [
        ['id' => 'SELLER-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'name' => 'Seller Two'],
    ];

    $page2Data = [
        ['id' => 'SELLER-3', 'name' => 'Seller Three'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturnUsing(function ($key, $callback) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);

            return $callback($item);
        });

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 0, 'size' => 500])
        ->andReturn($page1Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 1, 'size' => 500])
        ->andReturn($page2Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 2, 'size' => 500])
        ->andReturn([]);

    $result = $this->service->getAllSellers($this->accountId);

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(3)
        ->and($result[0]['id'])->toBe('SELLER-1')
        ->and($result[1]['id'])->toBe('SELLER-2')
        ->and($result[2]['id'])->toBe('SELLER-3');
});

\it('handles empty API response gracefully', function () {
    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturnUsing(function ($key, $callback) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);

            return $callback($item);
        });

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 0, 'size' => 500])
        ->andReturn([]);

    $result = $this->service->getAllSellers($this->accountId);

    \expect($result)->toBeArray()->and($result)->toBeEmpty();
});

\it('stops fetching when API returns empty array', function () {
    $page1Data = [
        ['id' => 'SELLER-1', 'name' => 'Seller One'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturnUsing(function ($key, $callback) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);

            return $callback($item);
        });

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 0, 'size' => 500])
        ->andReturn($page1Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 1, 'size' => 500])
        ->andReturn([]);

    $this->httpClient->shouldNotReceive('get')
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 2, 'size' => 500]);

    $result = $this->service->getAllSellers($this->accountId);

    \expect($result)->toHaveCount(1);
});

\it('returns specific seller by id when found', function () {
    $allSellers = [
        ['id' => 'SELLER-1', 'externalId' => 'EXT-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'externalId' => 'EXT-2', 'name' => 'Seller Two'],
        ['id' => 'SELLER-3', 'externalId' => 'EXT-3', 'name' => 'Seller Three'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturn($allSellers);

    $result = $this->service->getSeller('SELLER-2', $this->accountId);

    \expect($result)->toBeArray()
        ->and($result['id'])->toBe('SELLER-2')
        ->and($result['name'])->toBe('Seller Two');
});

\it('returns null when seller id is not found', function () {
    $allSellers = [
        ['id' => 'SELLER-1', 'externalId' => 'EXT-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'externalId' => 'EXT-2', 'name' => 'Seller Two'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturn($allSellers);

    $result = $this->service->getSeller('SELLER-999', $this->accountId);

    \expect($result)->toBeNull();
});

\it('returns null when getAllSellers returns null', function () {
    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturn(null);

    $result = $this->service->getSeller('SELLER-1', $this->accountId);

    \expect($result)->toBeNull();
});

\it('returns null when getAllSellers returns empty array', function () {
    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturn([]);

    $result = $this->service->getSeller('SELLER-1', $this->accountId);

    \expect($result)->toBeNull();
});

\it('caches result for 300 seconds', function () {
    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturnUsing(function ($key, $callback) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);

            return $callback($item);
        });

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 0, 'size' => 500])
        ->andReturn([]);

    $this->service->getAllSellers($this->accountId);
});

\it('merges multiple pages correctly', function () {
    $page1Data = [
        ['id' => 'SELLER-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'name' => 'Seller Two'],
    ];

    $page2Data = [
        ['id' => 'SELLER-3', 'name' => 'Seller Three'],
        ['id' => 'SELLER-4', 'name' => 'Seller Four'],
    ];

    $page3Data = [
        ['id' => 'SELLER-5', 'name' => 'Seller Five'],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with($this->cacheKey, Mockery::type('callable'))
        ->andReturnUsing(function ($key, $callback) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);

            return $callback($item);
        });

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 0, 'size' => 500])
        ->andReturn($page1Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 1, 'size' => 500])
        ->andReturn($page2Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 2, 'size' => 500])
        ->andReturn($page3Data);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SUPPLIERS->value, ['page' => 3, 'size' => 500])
        ->andReturn([]);

    $result = $this->service->getAllSellers($this->accountId);

    \expect($result)->toHaveCount(5)
        ->and($result[0]['id'])->toBe('SELLER-1')
        ->and($result[1]['id'])->toBe('SELLER-2')
        ->and($result[2]['id'])->toBe('SELLER-3')
        ->and($result[3]['id'])->toBe('SELLER-4')
        ->and($result[4]['id'])->toBe('SELLER-5');
});

\it('uses cache key based on channel', function () {
    $sellersChannelA = [['id' => 'SELLER-A', 'externalId' => 'EXT-A', 'name' => 'Seller A']];
    
    $this->cache->shouldReceive('get')
        ->once()
        ->with(DjustSellerService::SELLERS_CACHE_KEY.'_'.$this->channelCode, Mockery::type('callable'))
        ->andReturn($sellersChannelA);

    $result = $this->service->getAllSellers();

    \expect($result[0]['id'])->toBe('SELLER-A');
});

\it('uses channel-based cache key when no account provided', function () {
    $this->cache->shouldReceive('get')
        ->once()
        ->with(DjustSellerService::SELLERS_CACHE_KEY.'_'.$this->channelCode, Mockery::type('callable'))
        ->andReturn([]);

    $result = $this->service->getAllSellers();

    \expect($result)->toBeArray()->and($result)->toBeEmpty();
});

// ─── getAdherentSellerTarifIdMap() ───────────────────────────────────────────

\it('returns a map of sellerId => tarifId from accord-cadre FAT products (single page)', function () {
    $searchResult = [
        'products' => [
            'content' => [
                [
                    'supplier' => ['id' => 'SELLER-1'],
                    'offer' => [
                        'customFields' => [
                            ['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-uuid-1'],
                        ],
                    ],
                ],
                [
                    'supplier' => ['id' => 'SELLER-2'],
                    'offer' => [
                        'customFields' => [
                            ['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-uuid-2'],
                        ],
                    ],
                ],
            ],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($searchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($searchResult);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBe([
        'SELLER-1' => 'tarif-uuid-1',
        'SELLER-2' => 'tarif-uuid-2',
    ]);
})->group('UnitDjustSellerService');

\it('handles pagination and merges all pages for getAdherentSellerTarifIdMap', function () {
    $page0 = [
        'products' => [
            'content' => [
                [
                    'supplier' => ['id' => 'SELLER-1'],
                    'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-1']]],
                ],
            ],
            'totalPages' => 2,
        ],
    ];
    $page1 = [
        'products' => [
            'content' => [
                [
                    'supplier' => ['id' => 'SELLER-2'],
                    'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-2']]],
                ],
            ],
            'totalPages' => 2,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($page0, $page1) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->twice()
                ->andReturn($page0, $page1);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBe([
        'SELLER-1' => 'tarif-1',
        'SELLER-2' => 'tarif-2',
    ]);
})->group('UnitDjustSellerService');

\it('skips FAT items with no supplier id in getAdherentSellerTarifIdMap', function () {
    $searchResult = [
        'products' => [
            'content' => [
                [
                    'supplier' => [],
                    'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-1']]],
                ],
                [
                    'supplier' => ['id' => 'SELLER-2'],
                    'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-2']]],
                ],
            ],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($searchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($searchResult);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBe(['SELLER-2' => 'tarif-2']);
})->group('UnitDjustSellerService');

\it('skips FAT items with no OFFER_TARIF_ID custom field in getAdherentSellerTarifIdMap', function () {
    $searchResult = [
        'products' => [
            'content' => [
                [
                    'supplier' => ['id' => 'SELLER-1'],
                    'offer' => ['customFields' => [['externalId' => 'OTHER_FIELD', 'value' => 'some-value']]],
                ],
                [
                    'supplier' => ['id' => 'SELLER-2'],
                    'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-2']]],
                ],
            ],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($searchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($searchResult);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBe(['SELLER-2' => 'tarif-2']);
})->group('UnitDjustSellerService');

\it('returns empty map when no FAT products found in getAdherentSellerTarifIdMap', function () {
    $searchResult = [
        'products' => [
            'content' => [],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($searchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($searchResult);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBeArray()->and($result)->toBeEmpty();
})->group('UnitDjustSellerService');

\it('handles transformed custom field format (customField wrapper) in getAdherentSellerTarifIdMap', function () {
    $searchResult = [
        'products' => [
            'content' => [
                [
                    'supplier' => ['id' => 'SELLER-1'],
                    'offer' => [
                        'customFields' => [
                            [
                                'customField' => ['externalId' => 'OFFER_TARIF_ID'],
                                'value' => ['value' => 'tarif-wrapped'],
                            ],
                        ],
                    ],
                ],
            ],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->andReturnUsing(function ($key, $callback) use ($searchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($searchResult);
                
            return $callback($item);
        });

    $result = $this->service->getAdherentSellerTarifIdMap();

    \expect($result)->toBe(['SELLER-1' => 'tarif-wrapped']);
})->group('UnitDjustSellerService');

// ─── getValidSellers() ──────────────────────────────────────────────────────

\it('returns only ACTIVE sellers with accord-cadre and CMS page', function () {
    $allSellers = [
        ['id' => 'SELLER-1', 'supplierStatus' => 'ACTIVE'],
        ['id' => 'SELLER-2', 'supplierStatus' => 'ACTIVE'],
        ['id' => 'SELLER-3', 'supplierStatus' => 'INACTIVE'],
        ['id' => 'SELLER-4', 'supplierStatus' => 'ACTIVE'],
    ];

    $sellerTarifMap = [
        'SELLER-1' => 'tarif-1',
        'SELLER-2' => 'tarif-2',
        'SELLER-3' => 'tarif-3',
    ];

    $storyblokTarifIds = [
        'tarif-1' => true,
        'tarif-3' => true,
    ];

    $fatSearchResult = [
        'products' => [
            'content' => [
                ['supplier' => ['id' => 'SELLER-1'], 'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-1']]]],
                ['supplier' => ['id' => 'SELLER-2'], 'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-2']]]],
                ['supplier' => ['id' => 'SELLER-3'], 'offer' => ['customFields' => [['externalId' => 'OFFER_TARIF_ID', 'value' => 'tarif-3']]]],
            ],
            'totalPages' => 1,
        ],
    ];

    $this->cache->shouldReceive('get')
        ->once()
        ->with(DjustSellerService::SELLERS_CACHE_KEY.'_'.$this->channelCode, Mockery::any())
        ->andReturn($allSellers);

    $this->cache->shouldReceive('get')
        ->once()
        ->with(Mockery::pattern('/^djust_seller_tarif_map_'.$this->channelCode.'_/'), Mockery::any())
        ->andReturnUsing(function ($key, $callback) use ($fatSearchResult) {
            $item = Mockery::mock(ItemInterface::class);
            $item->shouldReceive('expiresAfter')->once()->with(300);
            
            $this->djustSearchService
                ->shouldReceive('search')
                ->once()
                ->andReturn($fatSearchResult);
                
            return $callback($item);
        });

    $this->accordCadreService->shouldReceive('getTarifIds')
        ->once()
        ->andReturn($storyblokTarifIds);

    $result = $this->service->getValidSellers('ACC123');

    \expect($result)->toHaveCount(1)
        ->and($result[0]['id'])->toBe('SELLER-1');
})->group('UnitDjustSellerService');

