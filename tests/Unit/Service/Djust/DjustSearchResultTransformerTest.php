<?php

declare(strict_types=1);

use App\Service\Djust\Search\Transformer\DjustSearchAttributeTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchNavigationTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchOfferPriceTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchOfferTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchPictureTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchProductTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchResultTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchSupplierTransformer;
use App\Service\Djust\Search\Transformer\DjustSearchVariantTransformer;

uses()->group('DjustSearchResultTransformer');

\beforeEach(function () {
    $pictureTransformer = new DjustSearchPictureTransformer();
    $attributeTransformer = new DjustSearchAttributeTransformer();
    $navigationTransformer = new DjustSearchNavigationTransformer();
    $variantTransformer = new DjustSearchVariantTransformer($pictureTransformer);
    $productTransformer = new DjustSearchProductTransformer($attributeTransformer, $navigationTransformer, $pictureTransformer);
    $offerTransformer = new DjustSearchOfferTransformer($variantTransformer);
    $offerPriceTransformer = new DjustSearchOfferPriceTransformer();
    $supplierTransformer = new DjustSearchSupplierTransformer();

    $this->transformer = new DjustSearchResultTransformer(
        $productTransformer,
        $offerTransformer,
        $offerPriceTransformer,
        $supplierTransformer
    );
});

\it('transforms complete search result item to getFullProduct format', function () {
    $searchItem = [
        'product' => [
            'id' => 'PROD-123',
            'sku' => 'SKU-123',
            'name' => 'Test Product',
            'description' => 'Test Description',
            'brand' => 'Test Brand',
            'externalId' => 'ext-123',
            'productUnit' => 'item',
            'tags' => [
                ['id' => 'TAG-1', 'name' => 'tag1'],
            ],
        ],
        'variant' => [
            'id' => 'VAR-123',
            'sku' => 'SKU-VAR-123',
            'name' => 'Variant Name',
            'description' => 'Variant Description',
            'ean' => '1234567890',
            'mpn' => 'MPN-123',
            'externalId' => 'var-ext-123',
            'pictureUrls' => [
                [
                    'url' => 'https://example.com/image.png?w=100',
                    'widthInPx' => 100,
                    'heightInPx' => 100,
                    'formatType' => 'PNG',
                    'sizeType' => 'SMALL',
                    'main' => false,
                ],
                [
                    'url' => 'https://example.com/image.png?w=1200',
                    'widthInPx' => 1200,
                    'heightInPx' => 1200,
                    'formatType' => 'PNG',
                    'sizeType' => 'LARGE',
                    'main' => true,
                ],
            ],
        ],
        'offer' => [
            'id' => 'OFFER-123',
            'quantityPerItem' => 2,
            'stock' => 100,
            'currency' => 'EUR',
            'packingType' => 'BOX',
            'productUnit' => 'item',
            'maxOrderQuantity' => 50,
            'minOrderQuantity' => 1,
            'customFields' => [],
        ],
        'offerPrice' => [
            'id' => 'PRICE-123',
            'externalId' => 'price-ext-123',
            'offerPriceType' => 'GROUP',
            'itemPerPack' => 1,
            'price' => 100.0,
            'itemPrice' => 100.0,
            'unitPrice' => 100.0,
            'unitPriceTTC' => 120.0,
            'discountItemPrice' => 90.0,
            'discountUnitPrice' => 90.0,
        ],
        'supplier' => [
            'id' => 'SUP-123',
            'externalId' => 'sup-ext-123',
            'name' => 'Test Supplier',
        ],
        'attributes' => [
            [
                'name' => 'Color',
                'externalId' => 'ATTR_COLOR',
                'value' => 'Red',
                'values' => [],
            ],
        ],
        'navigations' => [
            ['id' => 'NAV-1', 'externalId' => 'nav-1', 'name' => 'Category 1'],
            ['id' => 'NAV-2', 'externalId' => 'Root_default', 'name' => 'Root'],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result)->toBeArray()
        ->and($result)->toHaveKeys(['product', 'offers']);

    // Vérifier product
    \expect($result['product']['id'])->toBe('PROD-123')
        ->and($result['product']['sku'])->toBe('SKU-123')
        ->and($result['product']['brand'])->toBe('Test Brand')
        ->and($result['product']['productStatus'])->toBe('ACTIVE')
        ->and($result['product']['name'])->toBe(['fr-FR' => 'Test Product', 'FR' => 'Test Product'])
        ->and($result['product']['externalSource'])->toBe('CLIENT')
        ->and($result['product']['isBundle'])->toBe(false);

    // Vérifier offers
    \expect($result['offers'])->toHaveCount(1)
        ->and($result['offers'][0]['offerInventory']['id'])->toBe('OFFER-123')
        ->and($result['offers'][0]['offerInventory']['stock'])->toBe(100)
        ->and($result['offers'][0]['supplier']['name'])->toBe('Test Supplier');
});

\it('handles empty search item', function () {
    $result = $this->transformer->transformSearchResultItem([]);

    \expect($result)->toBeArray()
        ->and($result['product']['id'])->toBeNull()
        ->and($result['offers'][0]['offerInventory']['stock'])->toBe(0);
});

\it('transforms localized fields correctly', function () {
    $searchItem = [
        'product' => [
            'name' => ['fr-FR' => 'Produit', 'en-US' => 'Product'],
            'description' => 'Simple description',
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['name'])->toBe(['fr-FR' => 'Produit', 'en-US' => 'Product'])
        ->and($result['product']['description'])->toBe(['fr-FR' => 'Simple description', 'FR' => 'Simple description']);
});

\it('groups picture urls by base url', function () {
    $searchItem = [
        'variant' => [
            'pictureUrls' => [
                [
                    'url' => 'https://example.com/image1.png?w=100',
                    'widthInPx' => 100,
                    'heightInPx' => 100,
                    'formatType' => 'PNG',
                    'sizeType' => 'SMALL',
                    'main' => true,
                ],
                [
                    'url' => 'https://example.com/image1.png?w=1200',
                    'widthInPx' => 1200,
                    'heightInPx' => 1200,
                    'formatType' => 'PNG',
                    'sizeType' => 'LARGE',
                    'main' => true,
                ],
                [
                    'url' => 'https://example.com/image2.png?w=100',
                    'widthInPx' => 100,
                    'heightInPx' => 100,
                    'formatType' => 'PNG',
                    'sizeType' => 'SMALL',
                    'main' => false,
                ],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['productPictures'])->toHaveCount(2)
        ->and($result['product']['productPictures'][0]['urls'])->toHaveCount(2)
        ->and($result['product']['productPictures'][0]['isMain'])->toBe(true)
        ->and($result['product']['productPictures'][1]['urls'])->toHaveCount(1)
        ->and($result['product']['productPictures'][1]['isMain'])->toBe(false);
});

\it('extracts main image url correctly', function () {
    $searchItem = [
        'variant' => [
            'pictureUrls' => [
                [
                    'url' => 'https://example.com/small.png',
                    'sizeType' => 'SMALL',
                    'main' => true,
                ],
                [
                    'url' => 'https://example.com/large.png',
                    'sizeType' => 'LARGE',
                    'main' => true,
                ],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerInventory']['variant']['mainImageUrl'])->toBe('https://example.com/large.png');
});

\it('filters Root_default from navigations', function () {
    $searchItem = [
        'navigations' => [
            ['id' => 'NAV-1', 'externalId' => 'nav-1', 'name' => 'Category 1'],
            ['id' => 'NAV-2', 'externalId' => 'Root_default', 'name' => 'Root'],
            ['id' => 'NAV-3', 'externalId' => 'nav-3', 'name' => 'Category 3'],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['navigationCategories'])->toHaveCount(2)
        ->and($result['product']['navigationCategories'][0]['id'])->toBe('NAV-1')
        ->and($result['product']['navigationCategories'][1]['id'])->toBe('NAV-3');
});

\it('removes duplicate navigations', function () {
    $searchItem = [
        'navigations' => [
            ['id' => 'NAV-1', 'externalId' => 'nav-1', 'name' => 'Category 1'],
            ['id' => 'NAV-1-DUP', 'externalId' => 'nav-1', 'name' => 'Category 1 Duplicate'],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['navigationCategories'])->toHaveCount(1)
        ->and($result['product']['navigationCategories'][0]['id'])->toBe('NAV-1');
});

\it('transforms attributes to attributeValues', function () {
    $searchItem = [
        'attributes' => [
            [
                'name' => 'Color',
                'externalId' => 'ATTR_COLOR',
                'value' => 'Red',
                'values' => [],
            ],
            [
                'name' => 'Sizes',
                'externalId' => 'ATTR_SIZES',
                'value' => null,
                'values' => ['S', 'M', 'L'],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['attributeValues'])->toHaveCount(2)
        ->and($result['product']['attributeValues'][0]['attribute']['name'])->toBe(['fr-FR' => 'Color', 'FR' => 'Color'])
        ->and($result['product']['attributeValues'][0]['attribute']['type'])->toBe('TEXT')
        ->and($result['product']['attributeValues'][0]['value'])->toBe('Red')
        ->and($result['product']['attributeValues'][1]['attribute']['type'])->toBe('LIST_TEXT')
        ->and($result['product']['attributeValues'][1]['value'])->toBe(['S', 'M', 'L']);
});

\it('transforms product unit correctly', function () {
    $searchItem = [
        'product' => [
            'productUnit' => 'box',
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['productUnit'])->toBe(['type' => 'ITEM', 'unit' => 'box', 'id' => '134']);
});

\it('handles null product unit', function () {
    $searchItem = [
        'product' => [],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['productUnit'])->toBeNull();
});

\it('creates discount price when discount values exist', function () {
    $searchItem = [
        'offerPrice' => [
            'itemPrice' => 100.0,
            'unitPrice' => 100.0,
            'unitPriceTTC' => 120.0,
            'discountItemPrice' => 80.0,
            'discountUnitPrice' => 80.0,
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice'])->not()->toBeNull()
        ->and($result['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice']['itemPrice'])->toBe(80.0)
        ->and($result['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice']['unitPrice'])->toBe(80.0);
});

\it('does not create discount price when discount values are null', function () {
    $searchItem = [
        'offerPrice' => [
            'itemPrice' => 100.0,
            'unitPrice' => 100.0,
            'unitPriceTTC' => 120.0,
            'discountItemPrice' => null,
            'discountUnitPrice' => null,
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice'])->toBeNull();
});

\it('transforms custom fields to customFieldValues format', function () {
    $searchItem = [
        'offer' => [
            'customFields' => [
                [
                    'id' => 'CF-123',
                    'externalId' => 'CUSTOM_FIELD_1',
                    'name' => ['FR' => 'Field 1'],
                    'type' => 'TEXT',
                    'value' => 'Value 1',
                ],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerInventory']['customFieldValues'])->toHaveCount(1)
        ->and($result['offers'][0]['offerInventory']['customFieldValues'][0]['customField']['id'])->toBe('CF-123')
        ->and($result['offers'][0]['offerInventory']['customFieldValues'][0]['value']['value'])->toBe('Value 1');
});

\it('keeps custom fields already in correct format', function () {
    $searchItem = [
        'offer' => [
            'customFields' => [
                [
                    'customField' => ['id' => 'CF-123', 'externalId' => 'FIELD_1'],
                    'value' => 'Already formatted',
                ],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerInventory']['customFieldValues'][0])
        ->toBe([
            'customField' => ['id' => 'CF-123', 'externalId' => 'FIELD_1'],
            'value' => 'Already formatted',
        ]);
});

\it('skips custom fields without id or externalId', function () {
    $searchItem = [
        'offer' => [
            'customFields' => [
                [
                    'name' => 'Invalid Field',
                    'value' => 'Value',
                ],
            ],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerInventory']['customFieldValues'])->toBeEmpty();
});

\it('transforms variant with all fields', function () {
    $searchItem = [
        'product' => ['sku' => 'PROD-SKU'],
        'variant' => [
            'id' => 'VAR-123',
            'sku' => 'VAR-SKU',
            'name' => 'Variant Name',
            'description' => 'Variant Description',
            'ean' => '1234567890',
            'mpn' => 'MPN-123',
            'externalId' => 'var-ext-123',
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    $variant = $result['offers'][0]['offerInventory']['variant'];

    \expect($variant['id'])->toBe('VAR-123')
        ->and($variant['skuProduct'])->toBe('PROD-SKU')
        ->and($variant['skuVariant'])->toBe('VAR-SKU')
        ->and($variant['name'])->toBe('Variant Name')
        ->and($variant['description'])->toBe('Variant Description')
        ->and($variant['ean'])->toBe('1234567890')
        ->and($variant['mpn'])->toBe('MPN-123')
        ->and($variant['status'])->toBe('ACTIVE');
});

\it('handles empty picture urls', function () {
    $searchItem = [
        'variant' => [
            'pictureUrls' => [],
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['product']['productPictures'])->toBeEmpty()
        ->and($result['offers'][0]['offerInventory']['variant']['mainImageUrl'])->toBeNull()
        ->and($result['offers'][0]['offerInventory']['variant']['productMediaInfoDTO'])->toBeEmpty();
});

\it('uses fallback values for offer price', function () {
    $searchItem = [
        'offerPrice' => [
            'price' => 50.0,
        ],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    $price = $result['offers'][0]['offerPrices'][0]['priceRanges'][0]['price'];

    \expect($price['itemPrice'])->toBe(50.0)
        ->and($price['unitPrice'])->toBe(50.0);
});

\it('returns empty offerPrices when offerPrice is empty', function () {
    $searchItem = [
        'offerPrice' => [],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    \expect($result['offers'][0]['offerPrices'])->toBeEmpty();
});

\it('sets default values for offer inventory', function () {
    $searchItem = [
        'offer' => [],
    ];

    $result = $this->transformer->transformSearchResultItem($searchItem);

    $inventory = $result['offers'][0]['offerInventory'];

    \expect($inventory['quantityPerItem'])->toBe(1.0)
        ->and($inventory['stock'])->toBe(0)
        ->and($inventory['currency'])->toBe('EUR')
        ->and($inventory['packingType'])->toBe('UNIT')
        ->and($inventory['minOrderQuantity'])->toBe(0)
        ->and($inventory['status'])->toBe('ACTIVE');
});
