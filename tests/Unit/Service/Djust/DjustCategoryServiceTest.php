<?php

declare(strict_types=1);

use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Djust\DjustCategoryService;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\Search\DjustSearchService;
use App\Tests\Api\Helper\JsonHelper;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->searchService = Mockery::mock(DjustSearchService::class);

    $this->service = new DjustCategoryService(
        $this->httpClient,
        $this->searchService
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('filters categories and children according to search navigations', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');
    $searchFacets = JsonHelper::getJsonDataFile('_mocks/djust-response/products/search.json');

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    // Based on search-facets.json:
    // - 0000001001 (Électronique) - included
    // - 0000001002 (Ordinateurs) - included
    // - 0000002001 (Mobilier) - included
    // - 0000002002 (Bureau) - included
    // - 0000002003 (Chaises) - included
    // - 0000004001 (Consommables) - included
    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(3); // Électronique, Mobilier, Consommables

    // Check Électronique category
    \expect($result[0]['id'])->toBe('0000001001');
    \expect($result[0]['name'])->toBe('Électronique');
    \expect(\count($result[0]['childrenCategories']))->toBe(1); // Only Ordinateurs
    \expect($result[0]['childrenCategories'][0]['id'])->toBe('0000001002');
    \expect($result[0]['childrenCategories'][0]['parentId'])->toBe('0000001001');

    // Check Mobilier category
    \expect($result[1]['id'])->toBe('0000002001');
    \expect($result[1]['name'])->toBe('Mobilier');
    \expect(\count($result[1]['childrenCategories']))->toBe(1); // Only Bureau
    \expect($result[1]['childrenCategories'][0]['id'])->toBe('0000002002');
    \expect($result[1]['childrenCategories'][0]['parentId'])->toBe('0000002001');
    \expect(\count($result[1]['childrenCategories'][0]['childrenCategories']))->toBe(1); // Only Chaises
    \expect($result[1]['childrenCategories'][0]['childrenCategories'][0]['id'])->toBe('0000002003');

    // Check Consommables category
    \expect($result[2]['id'])->toBe('0000004001');
    \expect($result[2]['name'])->toBe('Consommables');
    \expect(\count($result[2]['childrenCategories']))->toBe(0); // No children in search facets
})->group('DjustCategoryService', 'Djust');

\it('returns empty array when no navigations are present', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn(['facets' => ['navigations' => []]]);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(0);
})->group('DjustCategoryService', 'Djust');

\it('handles missing facets by returning empty list', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn([]);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(0);
})->group('DjustCategoryService', 'Djust');

\it('filters nested children categories recursively', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    // Only include specific nested categories
    $searchFacets = [
        'facets' => [
            'navigations' => [
                ['id' => '0000002001'], // Mobilier
                ['id' => '0000002002'], // Bureau
                ['id' => '0000002003'], // Chaises (nested child)
            ],
        ],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(1); // Only Mobilier

    // Check Mobilier has Bureau child
    \expect($result[0]['id'])->toBe('0000002001');
    \expect(\count($result[0]['childrenCategories']))->toBe(1);
    \expect($result[0]['childrenCategories'][0]['id'])->toBe('0000002002');
    \expect($result[0]['childrenCategories'][0]['parentId'])->toBe('0000002001');

    // Check Bureau has Chaises nested child
    \expect(\count($result[0]['childrenCategories'][0]['childrenCategories']))->toBe(1);
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['id'])->toBe('0000002003');
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['parentId'])->toBe('0000002002');
})->group('DjustCategoryService', 'Djust');

\it('removes parent category when not in search but keeps if children match', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    // Include parent and child
    $searchFacets = [
        'facets' => [
            'navigations' => [
                ['id' => '0000001001'], // Électronique
                ['id' => '0000001005'], // Smartphones (child)
            ],
        ],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(1);
    \expect($result[0]['id'])->toBe('0000001001');
    \expect(\count($result[0]['childrenCategories']))->toBe(1); // Only Smartphones
    \expect($result[0]['childrenCategories'][0]['id'])->toBe('0000001005');
    \expect($result[0]['childrenCategories'][0]['parentId'])->toBe('0000001001');
})->group('DjustCategoryService', 'Djust');

\it('removes all children when parent in search but no children match', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    // Only include parent, no children
    $searchFacets = [
        'facets' => [
            'navigations' => [
                ['id' => '0000001001'], // Électronique only, no children
            ],
        ],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(1);
    \expect($result[0]['id'])->toBe('0000001001');
    \expect($result[0]['name'])->toBe('Électronique');
    \expect(\count($result[0]['childrenCategories']))->toBe(0); // All children filtered out
})->group('DjustCategoryService', 'Djust');

\it('filters deeply nested categories at any level', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    // Include deep nested path: Électronique -> Ordinateurs -> Portables
    $searchFacets = [
        'facets' => [
            'navigations' => [
                ['id' => '0000001001'], // Électronique
                ['id' => '0000001002'], // Ordinateurs
                ['id' => '0000001003'], // Portables (3rd level)
            ],
        ],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(1);
    \expect($result[0]['id'])->toBe('0000001001');
    \expect($result[0]['name'])->toBe('Électronique');

    // Check level 2
    \expect(\count($result[0]['childrenCategories']))->toBe(1);
    \expect($result[0]['childrenCategories'][0]['id'])->toBe('0000001002');
    \expect($result[0]['childrenCategories'][0]['parentId'])->toBe('0000001001');

    // Check level 3
    \expect(\count($result[0]['childrenCategories'][0]['childrenCategories']))->toBe(1);
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['id'])->toBe('0000001003');
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['name'])->toBe('Portables');
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['parentId'])->toBe('0000001002');
})->group('DjustCategoryService', 'Djust');

\it('filters multiple branches at different depths', function () {
    $allCategories = JsonHelper::getJsonDataFile('_mocks/djust-response/categories/full.json');

    // Mix of shallow and deep categories from different branches
    $searchFacets = [
        'facets' => [
            'navigations' => [
                ['id' => '0000001001'], // Électronique (level 1)
                ['id' => '0000001002'], // Ordinateurs (level 2)
                ['id' => '0000001004'], // Bureautique (level 3)
                ['id' => '0000004001'], // Consommables (level 1)
                ['id' => '0000004002'], // Papeterie (level 2)
                ['id' => '0000004003'], // Cahiers (level 3)
            ],
        ],
    ];

    $this->httpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value)
        ->andReturn($allCategories);

    $this->searchService
        ->shouldReceive('search')
        ->once()
        ->andReturn($searchFacets);

    $result = $this->service->getAvailableCategories();

    \expect($result)->toBeArray();
    \expect(\count($result))->toBe(2); // Électronique and Consommables

    // Verify first branch (Électronique)
    \expect($result[0]['id'])->toBe('0000001001');
    \expect($result[0]['childrenCategories'][0]['id'])->toBe('0000001002');
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['id'])->toBe('0000001004');
    \expect($result[0]['childrenCategories'][0]['childrenCategories'][0]['name'])->toBe('Bureautique');

    // Verify second branch (Consommables)
    \expect($result[1]['id'])->toBe('0000004001');
    \expect($result[1]['childrenCategories'][0]['id'])->toBe('0000004002');
    \expect($result[1]['childrenCategories'][0]['childrenCategories'][0]['id'])->toBe('0000004003');
    \expect($result[1]['childrenCategories'][0]['childrenCategories'][0]['name'])->toBe('Cahiers');
})->group('DjustCategoryService', 'Djust');
