<?php

declare(strict_types=1);

use App\Factory\CategoryFactory;
use App\Factory\SellerFactory;
use App\Service\Djust\DjustCategoryService;
use App\Service\Djust\Search\DjustSearchFiltersBuilder;

\uses()->group('UnitDjustSearchFiltersBuilder');

\beforeEach(function () {
    $this->categoryFactory = \Mockery::mock(CategoryFactory::class);
    $this->djustCategoryService = \Mockery::mock(DjustCategoryService::class);
    $this->sellerFactory = \Mockery::mock(SellerFactory::class);

    $this->builder = new DjustSearchFiltersBuilder(
        $this->categoryFactory,
        $this->djustCategoryService,
        $this->sellerFactory
    );
});

\afterEach(function () {
    \Mockery::close();
});

\it('returns empty array when no filters are provided', function () {
    $remoteFilters = [];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('builds filter with suppliers only', function () {
    $suppliers = [
        ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
        ['id' => 'SUPP-2', 'name' => 'Supplier 2'],
    ];

    $expectedSellers = [
        (object) ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
        (object) ['id' => 'SUPP-2', 'name' => 'Supplier 2'],
    ];

    $remoteFilters = [
        'suppliers' => $suppliers,
    ];

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn($expectedSellers);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('sellers')
        ->and($result['sellers'])->toBe($expectedSellers)
        ->and($result)->not()->toHaveKey('categories');
});

\it('builds filter with navigations only', function () {
    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Category 1'],
        ['id' => 'NAV-2', 'name' => 'Category 2'],
    ];

    $availableCategories = [
        ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Category 2'],
        ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Category 3'],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Category 1'],
        (object) ['id' => 'NAV-2', 'name' => 'Category 2'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([
            ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
            ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Category 2'],
        ])
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories)
        ->and($result)->not()->toHaveKey('sellers');
});

\it('builds filter with both suppliers and navigations', function () {
    $suppliers = [
        ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
    ];

    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Category 1'],
    ];

    $availableCategories = [
        ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Category 2'],
    ];

    $expectedSellers = [
        (object) ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Category 1'],
    ];

    $remoteFilters = [
        'suppliers' => $suppliers,
        'navigations' => $navigations,
    ];

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn($expectedSellers);

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([
            ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ])
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKeys(['sellers', 'categories'])
        ->and($result['sellers'])->toBe($expectedSellers)
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('filters out categories not in navigation list', function () {
    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Category 1'],
        ['id' => 'NAV-3', 'name' => 'Category 3'],
    ];

    $availableCategories = [
        ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Category 2'],
        ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Category 3'],
        ['id' => 'NAV-4', 'externalId' => 'ext-4', 'name' => 'Category 4'],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Category 1'],
        (object) ['id' => 'NAV-3', 'name' => 'Category 3'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([
            ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
            ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Category 3'],
        ])
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('returns empty array when suppliers is empty array', function () {
    $remoteFilters = [
        'suppliers' => [],
    ];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('returns empty array when navigations is empty array', function () {
    $remoteFilters = [
        'navigations' => [],
    ];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('does not include categories when no matching categories found', function () {
    $navigations = [
        ['id' => 'NAV-999', 'name' => 'Non-existent Category'],
    ];

    $availableCategories = [
        ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Category 2'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('handles null available categories', function () {
    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Category 1'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn([]);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('handles suppliers with multiple items', function () {
    $suppliers = [
        ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
        ['id' => 'SUPP-2', 'name' => 'Supplier 2'],
        ['id' => 'SUPP-3', 'name' => 'Supplier 3'],
        ['id' => 'SUPP-4', 'name' => 'Supplier 4'],
    ];

    $expectedSellers = \array_map(fn ($s) => (object) $s, $suppliers);

    $remoteFilters = [
        'suppliers' => $suppliers,
    ];

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn($expectedSellers);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('sellers')
        ->and($result['sellers'])->toHaveCount(4);
});

\it('builds filter with properties only', function () {
    $attributes = [
        ['id' => 'ATTR-1', 'name' => 'Color', 'value' => 'Red'],
        ['id' => 'ATTR-2', 'name' => 'Size', 'value' => 'Large'],
    ];

    $remoteFilters = [
        'attributes' => $attributes,
    ];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('properties')
        ->and($result['properties'])->toBe($attributes)
        ->and($result)->not()->toHaveKey('sellers')
        ->and($result)->not()->toHaveKey('categories');
});

\it('builds filter with suppliers, navigations and properties', function () {
    $suppliers = [
        ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
    ];

    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Category 1'],
    ];

    $attributes = [
        ['id' => 'ATTR-1', 'name' => 'Color', 'value' => 'Blue'],
    ];

    $availableCategories = [
        ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
    ];

    $expectedSellers = [
        (object) ['id' => 'SUPP-1', 'name' => 'Supplier 1'],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Category 1'],
    ];

    $remoteFilters = [
        'suppliers' => $suppliers,
        'navigations' => $navigations,
        'attributes' => $attributes,
    ];

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn($expectedSellers);

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([
            ['id' => 'NAV-1', 'externalId' => 'ext-1', 'name' => 'Category 1'],
        ])
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKeys(['sellers', 'categories', 'properties'])
        ->and($result['sellers'])->toBe($expectedSellers)
        ->and($result['categories'])->toBe($expectedCategories)
        ->and($result['properties'])->toBe($attributes);
});

\it('restricts sellers to requested ones by externalId', function () {
    $suppliers = [
        ['id' => '1', 'name' => 'Supplier 1'],
        ['id' => '2', 'name' => 'Supplier 2'],
    ];

    $seller1 = new \App\Dto\Seller();
    $seller1->setId('1')->setExternalId('EXT-1');
    $seller2 = new \App\Dto\Seller();
    $seller2->setId('2')->setExternalId('EXT-2');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn([$seller1, $seller2]);

    $result = $this->builder->buildFilter(
        ['suppliers' => $suppliers],
        ['sellers' => ['EXT-1']],
    );

    \expect($result['sellers'])->toHaveCount(1)
        ->and($result['sellers'][0]->getExternalId())->toBe('EXT-1');
});

\it('restricts sellers to requested ones by id', function () {
    $suppliers = [
        ['id' => '1', 'name' => 'Supplier 1'],
        ['id' => '2', 'name' => 'Supplier 2'],
    ];

    $seller1 = new \App\Dto\Seller();
    $seller1->setId('1')->setExternalId('EXT-1');
    $seller2 = new \App\Dto\Seller();
    $seller2->setId('2')->setExternalId('EXT-2');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn([$seller1, $seller2]);

    $result = $this->builder->buildFilter(
        ['suppliers' => $suppliers],
        ['sellers' => ['2']],
    );

    \expect($result['sellers'])->toHaveCount(1)
        ->and($result['sellers'][0]->getId())->toBe('2');
});

\it('returns all sellers when requestedFilters is empty', function () {
    $suppliers = [
        ['id' => '1', 'name' => 'Supplier 1'],
        ['id' => '2', 'name' => 'Supplier 2'],
    ];

    $seller1 = new \App\Dto\Seller();
    $seller1->setId('1')->setExternalId('EXT-1');
    $seller2 = new \App\Dto\Seller();
    $seller2->setId('2')->setExternalId('EXT-2');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn([$seller1, $seller2]);

    $result = $this->builder->buildFilter(['suppliers' => $suppliers], []);

    \expect($result['sellers'])->toHaveCount(2);
});

\it('returns empty sellers when requestedFilters does not match any seller', function () {
    $suppliers = [
        ['id' => '1', 'name' => 'Supplier 1'],
    ];

    $seller1 = new \App\Dto\Seller();
    $seller1->setId('1')->setExternalId('EXT-1');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($suppliers)
        ->andReturn([$seller1]);

    $result = $this->builder->buildFilter(
        ['suppliers' => $suppliers],
        ['sellers' => ['EXT-999']],
    );

    \expect($result)->not()->toHaveKey('sellers');
});

\it('returns empty array when attributes is empty array', function () {
    $remoteFilters = [
        'attributes' => [],
    ];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('handles properties with multiple items', function () {
    $attributes = [
        ['id' => 'ATTR-1', 'name' => 'Color', 'value' => 'Red'],
        ['id' => 'ATTR-2', 'name' => 'Size', 'value' => 'Large'],
        ['id' => 'ATTR-3', 'name' => 'Brand', 'value' => 'Nike'],
        ['id' => 'ATTR-4', 'name' => 'Material', 'value' => 'Cotton'],
    ];

    $remoteFilters = [
        'attributes' => $attributes,
    ];

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('properties')
        ->and($result['properties'])->toBe($attributes)
        ->and($result['properties'])->toHaveCount(4);
});

\it('filters categories with nested children when child matches', function () {
    $navigations = [
        ['id' => 'NAV-2', 'name' => 'Child Category'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Other Child'],
            ],
        ],
    ];

    $expectedFilteredCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
            ],
        ],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($expectedFilteredCategories)
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('filters categories with nested children when parent matches', function () {
    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Other Child'],
            ],
        ],
    ];

    $expectedFilteredCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
        ],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($expectedFilteredCategories)
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('filters categories with multiple matching children', function () {
    $navigations = [
        ['id' => 'NAV-2', 'name' => 'Child Category 1'],
        ['id' => 'NAV-3', 'name' => 'Child Category 2'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category 1'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Child Category 2'],
                ['id' => 'NAV-4', 'externalId' => 'ext-4', 'name' => 'Child Category 3'],
            ],
        ],
    ];

    $expectedFilteredCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category 1'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Child Category 2'],
            ],
        ],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($expectedFilteredCategories)
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('filters deeply nested categories', function () {
    $navigations = [
        ['id' => 'NAV-3', 'name' => 'Deep Child Category'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                [
                    'id' => 'NAV-2',
                    'externalId' => 'ext-2',
                    'name' => 'Child Category',
                    'childrenCategories' => [
                        ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Deep Child Category'],
                        ['id' => 'NAV-4', 'externalId' => 'ext-4', 'name' => 'Other Deep Child'],
                    ],
                ],
            ],
        ],
    ];

    $expectedFilteredCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                [
                    'id' => 'NAV-2',
                    'externalId' => 'ext-2',
                    'name' => 'Child Category',
                    'childrenCategories' => [
                        ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Deep Child Category'],
                    ],
                ],
            ],
        ],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($expectedFilteredCategories)
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});

\it('filters out parent when no children match', function () {
    $navigations = [
        ['id' => 'NAV-999', 'name' => 'Non-existent Category'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Other Child'],
            ],
        ],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toBe([]);
});

\it('keeps both parent and matching child when both match', function () {
    $navigations = [
        ['id' => 'NAV-1', 'name' => 'Parent Category'],
        ['id' => 'NAV-2', 'name' => 'Child Category'],
    ];

    $availableCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
                ['id' => 'NAV-3', 'externalId' => 'ext-3', 'name' => 'Other Child'],
            ],
        ],
    ];

    $expectedFilteredCategories = [
        [
            'id' => 'NAV-1',
            'externalId' => 'ext-1',
            'name' => 'Parent Category',
            'childrenCategories' => [
                ['id' => 'NAV-2', 'externalId' => 'ext-2', 'name' => 'Child Category'],
            ],
        ],
    ];

    $expectedCategories = [
        (object) ['id' => 'NAV-1', 'name' => 'Parent Category'],
    ];

    $remoteFilters = [
        'navigations' => $navigations,
    ];

    $this->djustCategoryService
        ->shouldReceive('getAvailableCategories')
        ->once()
        ->andReturn($availableCategories);

    $this->categoryFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($expectedFilteredCategories)
        ->andReturn($expectedCategories);

    $result = $this->builder->buildFilter($remoteFilters);

    \expect($result)->toHaveKey('categories')
        ->and($result['categories'])->toBe($expectedCategories);
});
