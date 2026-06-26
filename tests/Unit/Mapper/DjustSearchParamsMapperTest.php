<?php

declare(strict_types=1);

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustDefaults;
use App\Mapper\DjustSearchParamsMapper;

\uses()->group('DjustSearchParamsMapperUnit');

\beforeEach(function () {
    $this->mapper = new DjustSearchParamsMapper();
});

\it('maps all filters from context', function () {
    $context = [
        'filters' => [
            'name' => 'test product',
            'locale' => 'en-US',
            'page' => '2',
            'perPage' => '50',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBe('test product')
        ->and($result->locale)->toBe('en-US')
        ->and($result->page)->toBe('2')
        ->and($result->size)->toBe('50')
        ->and($result->categoryIds)->toBeNull()
        ->and($result->suppliers)->toBeNull()
        ->and($result->attributes)->toBeNull();
});

\it('uses default values when filters are missing', function () {
    $context = [];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBeNull()
        ->and($result->locale)->toBe(DjustDefaults::LOCALE->value)
        ->and($result->page)->toBe(DjustDefaults::SEARCH_PAGE_NUMBER->value)
        ->and($result->size)->toBe(DjustDefaults::SEARCH_PER_PAGE_PRODUCT->value)
        ->and($result->categoryIds)->toBeNull()
        ->and($result->suppliers)->toBeNull()
        ->and($result->attributes)->toBeNull();
});

\it('uses default values when filters array is empty', function () {
    $context = [
        'filters' => [],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBeNull()
        ->and($result->locale)->toBe(DjustDefaults::LOCALE->value)
        ->and($result->page)->toBe(DjustDefaults::SEARCH_PAGE_NUMBER->value)
        ->and($result->size)->toBe(DjustDefaults::SEARCH_PER_PAGE_PRODUCT->value);
});

\it('uses default locale when locale filter is missing', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'page' => '1',
            'perPage' => '20',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->locale)->toBe(DjustDefaults::LOCALE->value)
        ->and($result->query)->toBe('test')
        ->and($result->page)->toBe('1')
        ->and($result->size)->toBe('20');
});

\it('uses default page when page filter is missing', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'locale' => 'fr-FR',
            'perPage' => '20',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->page)->toBe(DjustDefaults::SEARCH_PAGE_NUMBER->value)
        ->and($result->query)->toBe('test')
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->size)->toBe('20');
});

\it('uses default perPage when perPage filter is missing', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'locale' => 'fr-FR',
            'page' => '3',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->size)->toBe(DjustDefaults::SEARCH_PER_PAGE_PRODUCT->value)
        ->and($result->query)->toBe('test')
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->page)->toBe('3');
});

\it('handles null query gracefully', function () {
    $context = [
        'filters' => [
            'name' => null,
            'locale' => 'fr-FR',
            'page' => '1',
            'perPage' => '20',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->query)->toBeNull()
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->page)->toBe('1')
        ->and($result->size)->toBe('20');
});

\it('handles empty string query', function () {
    $context = [
        'filters' => [
            'name' => '',
            'locale' => 'fr-FR',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->query)->toBe('');
});

\it('accepts different locale values', function () {
    $locales = ['fr-FR', 'en-US', 'de-DE', 'es-ES'];

    foreach ($locales as $locale) {
        $context = [
            'filters' => [
                'locale' => $locale,
            ],
        ];

        $result = $this->mapper->fromContext($context);

        \expect($result->locale)->toBe($locale);
    }
});

\it('accepts different page number values', function () {
    $pages = ['0', '1', '5', '10', '100'];

    foreach ($pages as $page) {
        $context = [
            'filters' => [
                'page' => $page,
            ],
        ];

        $result = $this->mapper->fromContext($context);

        \expect($result->page)->toBe($page);
    }
});

\it('accepts different perPage values', function () {
    $perPageValues = ['10', '20', '36', '50', '100'];

    foreach ($perPageValues as $perPage) {
        $context = [
            'filters' => [
                'perPage' => $perPage,
            ],
        ];

        $result = $this->mapper->fromContext($context);

        \expect($result->size)->toBe($perPage);
    }
});

\it('handles special characters in query', function () {
    $context = [
        'filters' => [
            'name' => 'café & thé @ 50% discount!',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->query)->toBe('café & thé @ 50% discount!');
});

\it('handles very long query strings', function () {
    $longQuery = \str_repeat('test ', 100);

    $context = [
        'filters' => [
            'name' => $longQuery,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->query)->toBe($longQuery);
});

\it('ignores extra filters not used by mapper', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'locale' => 'fr-FR',
            'page' => '1',
            'perPage' => '20',
            'categoryId' => '123',
            'brandId' => '456',
            'minPrice' => '10',
            'maxPrice' => '100',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBe('test')
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->page)->toBe('1')
        ->and($result->size)->toBe('20');
});

\it('creates immutable DjustSearchParams object', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'locale' => 'fr-FR',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and(\property_exists($result, 'query'))->toBeTrue()
        ->and(\property_exists($result, 'locale'))->toBeTrue()
        ->and(\property_exists($result, 'page'))->toBeTrue()
        ->and(\property_exists($result, 'size'))->toBeTrue();
});

\it('handles numeric string values correctly', function () {
    $context = [
        'filters' => [
            'page' => '0',
            'perPage' => '36',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->page)->toBe('0')
        ->and($result->size)->toBe('36')
        ->and($result->page)->toBeString()
        ->and($result->size)->toBeString();
});

\it('maps properties and formats attributes correctly', function () {
    $properties = \json_encode([
        'property_id' => 'color',
        'value' => 'red',
    ]);

    $context = [
        'filters' => [
            'properties' => $properties,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBe(['color|red']);
});

\it('maps categories filter', function () {
    $context = [
        'filters' => [
            'categories' => 'cat-123',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->categoryIds)->toBe('cat-123')
        ->and($result->attributes)->toBeNull()
        ->and($result->suppliers)->toBeNull();
});

\it('maps sellers filter', function () {
    $context = [
        'filters' => [
            'sellers' => ['seller-456'],
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->suppliers)->toBe(['seller-456'])
        ->and($result->categoryIds)->toBeNull()
        ->and($result->attributes)->toBeNull();
});

\it('maps all filter types together', function () {
    $properties = \json_encode([
        'property_id' => 'size',
        'value' => 'large',
    ]);

    $context = [
        'filters' => [
            'name' => 'test product',
            'locale' => 'fr-FR',
            'page' => '3',
            'perPage' => '24',
            'categories' => 'cat-789',
            'sellers' => ['seller-101'],
            'properties' => $properties,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBe('test product')
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->page)->toBe('3')
        ->and($result->size)->toBe('24')
        ->and($result->categoryIds)->toBe('cat-789')
        ->and($result->suppliers)->toBe(['seller-101'])
        ->and($result->attributes)->toBe(['size|large']);
});

\it('accepts properties as PHP array with single item', function () {
    $context = [
        'filters' => [
            'properties' => [
                ['property_id' => 'color', 'value' => 'red'],
            ],
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBe(['color|red']);
});

\it('accepts properties as PHP array with multiple items', function () {
    $context = [
        'filters' => [
            'properties' => [
                ['property_id' => 'PRODUCT_TYPE', 'value' => 'SELLABLE'],
                ['property_id' => 'PRODUCT_TYPE', 'value' => 'NOT_SELLABLE'],
            ],
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBe(['PRODUCT_TYPE|SELLABLE', 'PRODUCT_TYPE|NOT_SELLABLE']);
});

\it('throws InvalidArgumentException on invalid attributes JSON', function () {
    $context = [
        'filters' => [
            'properties' => 'not-valid-json',
        ],
    ];

    $this->mapper->fromContext($context);
})->throws(\InvalidArgumentException::class);

\it('throws InvalidArgumentException when attributes JSON is missing required keys', function () {
    $context = [
        'filters' => [
            'properties' => \json_encode(['foo' => 'bar']),
        ],
    ];

    $this->mapper->fromContext($context);
})->throws(\InvalidArgumentException::class);

\it('handles null properties gracefully', function () {
    $context = [
        'filters' => [
            'properties' => null,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBeNull();
});

\it('formats attributes with different property types', function () {
    $properties = \json_encode([
        'property_id' => 'material',
        'value' => 'cotton',
    ]);

    $context = [
        'filters' => [
            'properties' => $properties,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBe(['material|cotton']);
});

\it('formats attributes with numeric values', function () {
    $properties = \json_encode([
        'property_id' => 'weight',
        'value' => '150',
    ]);

    $context = [
        'filters' => [
            'properties' => $properties,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->attributes)->toBe(['weight|150']);
});

\it('handles multiple categories', function () {
    $context = [
        'filters' => [
            'categories' => 'cat-1,cat-2,cat-3',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->categoryIds)->toBe('cat-1,cat-2,cat-3');
});

\it('handles multiple sellers', function () {
    $context = [
        'filters' => [
            'sellers' => ['seller-1', 'seller-2'],
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->suppliers)->toBe(['seller-1', 'seller-2']);
});

\it('maps productTags filter when provided', function () {
    $context = [
        'filters' => [
            'productTags' => 'HOMEPAGE_PRODUCT_SELECTION_QANTIS_ACHAT',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->productTags)->toBe('HOMEPAGE_PRODUCT_SELECTION_QANTIS_ACHAT');
});

\it('handles productTags with null value', function () {
    $context = [
        'filters' => [
            'productTags' => null,
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result->productTags)->toBeNull();
});

\it('maps all filters including productTags and categoryIds', function () {
    $context = [
        'filters' => [
            'name' => 'test',
            'locale' => 'fr-FR',
            'page' => '1',
            'perPage' => '50',
            'categories' => '123,456',
            'productTags' => 'HOMEPAGE_ACCORD_SELECTION_QANTIS_ACHAT',
        ],
    ];

    $result = $this->mapper->fromContext($context);

    \expect($result)->toBeInstanceOf(DjustSearchParams::class)
        ->and($result->query)->toBe('test')
        ->and($result->locale)->toBe('fr-FR')
        ->and($result->page)->toBe('1')
        ->and($result->size)->toBe('50')
        ->and($result->categoryIds)->toBe('123,456')
        ->and($result->productTags)->toBe('HOMEPAGE_ACCORD_SELECTION_QANTIS_ACHAT');
});
