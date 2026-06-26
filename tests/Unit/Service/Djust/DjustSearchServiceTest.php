<?php

declare(strict_types=1);

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Mapper\DjustSearchParamsMapper;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\Search\DjustSearchService;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->mapper = Mockery::mock(DjustSearchParamsMapper::class);

    $this->service = new DjustSearchService(
        $this->httpClient,
        $this->mapper,
    );
});

\afterEach(function () {
    Mockery::close();
});

// ─── search() ────────────────────────────────────────────────────────────────

\it('calls http client with params toArray', function () {
    $params = new DjustSearchParams(query: 'test');

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SEARCH->value, $params->toArray())
        ->andReturn(['products' => []]);

    $result = $this->service->search($params);

    \expect($result)->toBe(['products' => []]);
})->group('DjustSearchService', 'Djust');

\it('uses default params when none provided', function () {
    $defaultParams = new DjustSearchParams();

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SEARCH->value, $defaultParams->toArray())
        ->andReturn(['products' => []]);

    $this->service->search();
})->group('DjustSearchService', 'Djust');

\it('passes attributes in query params', function () {
    $params = new DjustSearchParams(attributes: ['PRODUCT_TYPE|SELLABLE', 'COLOR|red']);

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_SEARCH->value, $params->toArray())
        ->andReturn(['products' => []]);

    $this->service->search($params);
})->group('DjustSearchService', 'Djust');

// ─── searchSplit() ────────────────────────────────────────────────────────────

\it('searchSplit returns accordCadres, products and merged facets', function () {
    $context = ['filters' => ['name' => 'test']];
    $params = new DjustSearchParams(query: 'test');

    $this->mapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn($params);

    $fatResponse = [
        'products' => [
            'content' => [['id' => 'fat-1']],
            'totalPages' => 1,
            'totalElements' => 1,
        ],
        'facets' => ['suppliers' => ['supplier-A']],
    ];

    $productResponse = [
        'products' => [
            'content' => [['id' => 'prod-1']],
            'totalPages' => 1,
            'totalElements' => 1,
            'pageable' => ['pageNumber' => 0],
        ],
        'facets' => ['suppliers' => ['supplier-B']],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->twice()
        ->andReturn($fatResponse, $productResponse);

    $result = $this->service->searchSplit($context);

    \expect($result['accordCadres']['content'])->toBe([['id' => 'fat-1']])
        ->and($result['accordCadres']['totalElements'])->toBe(1)
        ->and($result['products'])->toBe($productResponse['products'])
        ->and($result['facets']['suppliers'])->toContain('supplier-A')
        ->and($result['facets']['suppliers'])->toContain('supplier-B');
})->group('DjustSearchService', 'Djust');

\it('searchSplit fetches all FAT pages when totalPages > 1', function () {
    $context = ['filters' => []];
    $params = new DjustSearchParams();

    $this->mapper
        ->shouldReceive('fromContext')
        ->once()
        ->andReturn($params);

    $fatPage0 = [
        'products' => ['content' => [['id' => 'fat-0']], 'totalPages' => 2],
        'facets' => [],
    ];
    $fatPage1 = [
        'products' => ['content' => [['id' => 'fat-1']], 'totalPages' => 2],
        'facets' => [],
    ];
    $productResponse = [
        'products' => ['content' => [], 'totalPages' => 1],
        'facets' => [],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->times(3)
        ->andReturn($fatPage0, $fatPage1, $productResponse);

    $result = $this->service->searchSplit($context);

    \expect($result['accordCadres']['content'])->toBe([['id' => 'fat-0'], ['id' => 'fat-1']])
        ->and($result['accordCadres']['totalElements'])->toBe(2);
})->group('DjustSearchService', 'Djust');

\it('searchSplit excludes ACCORD_CADRE from product attributes', function () {
    $context = ['filters' => []];
    $params = new DjustSearchParams(attributes: ['PRODUCT_TYPE|ACCORD_CADRE', 'COLOR|red']);

    $this->mapper
        ->shouldReceive('fromContext')
        ->once()
        ->andReturn($params);

    $fatResponse = [
        'products' => ['content' => [], 'totalPages' => 1],
        'facets' => [],
    ];

    $capturedQueryParams = [];

    $this->httpClient
        ->shouldReceive('get')
        ->twice()
        ->andReturnUsing(function (string $endpoint, array $queryParams) use (&$capturedQueryParams, $fatResponse) {
            $capturedQueryParams[] = $queryParams;

            return $fatResponse;
        });

    $this->service->searchSplit($context);

    $productAttributes = $capturedQueryParams[1]['attributes'] ?? [];
    \expect($productAttributes)->toContain('COLOR|red')
        ->and($productAttributes)->not->toContain('PRODUCT_TYPE|ACCORD_CADRE');
})->group('DjustSearchService', 'Djust');

// ─── aggregation propagation ──────────────────────────────────────────────────

\it('searchSplit propagates aggregation to FAT params', function () {
    $context = ['filters' => []];
    $params = new DjustSearchParams(aggregation: 'PRODUCT');

    $this->mapper->shouldReceive('fromContext')->once()->andReturn($params);

    $fatResponse = ['products' => ['content' => [], 'totalPages' => 1], 'facets' => []];

    $capturedQueryParams = [];

    $this->httpClient
        ->shouldReceive('get')
        ->twice()
        ->andReturnUsing(function (string $endpoint, array $queryParams) use (&$capturedQueryParams, $fatResponse) {
            $capturedQueryParams[] = $queryParams;

            return $fatResponse;
        });

    $this->service->searchSplit($context);

    \expect($capturedQueryParams[0]['aggregation'] ?? null)->toBe('PRODUCT');
})->group('DjustSearchService', 'Djust');

// ─── fat_priority sorting ─────────────────────────────────────────────────────

\it('sorts accordCadres with fat_priority tag first', function () {
    $context = ['filters' => []];
    $params = new DjustSearchParams();

    $this->mapper->shouldReceive('fromContext')->once()->andReturn($params);

    $fatResponse = [
        'products' => [
            'content' => [
                ['id' => 'fat-normal', 'product' => ['tags' => []]],
                ['id' => 'fat-priority', 'product' => ['tags' => [['name' => 'fat_priority']]]],
                ['id' => 'fat-normal-2', 'product' => ['tags' => [['name' => 'other_tag']]]],
            ],
            'totalPages' => 1,
        ],
        'facets' => [],
    ];
    $productResponse = ['products' => ['content' => [], 'totalPages' => 1], 'facets' => []];

    $this->httpClient->shouldReceive('get')->twice()->andReturn($fatResponse, $productResponse);

    $result = $this->service->searchSplit($context);

    \expect($result['accordCadres']['content'][0]['id'])->toBe('fat-priority')
        ->and($result['accordCadres']['content'][1]['id'])->toBe('fat-normal')
        ->and($result['accordCadres']['content'][2]['id'])->toBe('fat-normal-2');
})->group('DjustSearchService', 'Djust');

// ─── mergeFacets() ────────────────────────────────────────────────────────────

\it('merges facets from FAT and product results without duplicates', function () {
    $context = ['filters' => []];
    $params = new DjustSearchParams();

    $this->mapper->shouldReceive('fromContext')->once()->andReturn($params);

    $fatResponse = [
        'products' => ['content' => [], 'totalPages' => 1],
        'facets' => ['suppliers' => ['A', 'B'], 'categories' => ['cat-1']],
    ];
    $productResponse = [
        'products' => ['content' => [], 'totalPages' => 1],
        'facets' => ['suppliers' => ['B', 'C']],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->twice()
        ->andReturn($fatResponse, $productResponse);

    $result = $this->service->searchSplit($context);

    \expect($result['facets']['suppliers'])->toBe(['B', 'C', 'A'])
        ->and($result['facets']['categories'])->toBe(['cat-1']);
})->group('DjustSearchService', 'Djust');
