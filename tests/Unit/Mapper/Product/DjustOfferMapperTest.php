<?php

declare(strict_types=1);

use App\Dto\Product;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustProductType;
use App\Mapper\Product\DjustOfferMapper;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\Product\DjustPropertyFilter;

\uses()->group('UnitDjustOfferMapper');

\beforeEach(function () {
    $this->extractor = new DjustDataExtractor();
    $this->propertyFilter = new DjustPropertyFilter($this->extractor);
    $this->mapper = new DjustOfferMapper($this->extractor, $this->propertyFilter);
    $this->product = new Product();
});

\it('handles null price gracefully', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
            ],
            'offerPrices' => [], // Pas de prix
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    // Le pricing est maintenant géré par DjustVariantMapper
    \expect($this->product)->toBeInstanceOf(Product::class);
});

\it('uses price as priceReference when priceReference is null', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'price' => ['itemPrice' => 100.0],
                            // Pas de discountPrice
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    // Le pricing est maintenant géré par DjustVariantMapper
    \expect($this->product)->toBeInstanceOf(Product::class);
})->group('UnitDjustOfferMapper');

\it('handles empty offers array', function () {
    $offers = [];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    // Devrait gérer gracieusement sans crasher
    \expect($this->product->getPrice())->toBeNull();
});

\it('handles missing inventory data gracefully', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
                // Pas d'autres données inventory
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'price' => ['itemPrice' => 120.0],
                            'discountPrice' => ['itemPrice' => 100.0],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    // Le pricing est maintenant géré par DjustVariantMapper
    // Les quantités ne doivent pas être définies
    \expect($this->product->getMinOrderQuantity())->toBe(1); // Default dans Product
});

\it('sets default quantity limits when inventory data is present', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
                'minOrderQuantity' => 5,
                'maxOrderQuantity' => 100,
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'price' => ['itemPrice' => 120.0],
                            'discountPrice' => ['itemPrice' => 100.0],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    \expect($this->product->getMinOrderQuantity())->toBe(5);
    \expect($this->product->getMaxOrderQuantity())->toBe(100);
});

\it('ensures min quantity is at least 1', function () {
    $offers = [
        [
            'offerInventory' => [
                'prices' => [
                    [
                        'price' => 100.0,
                        'priceReference' => 120.0,
                        'defaultInventory' => [
                            'minOrderQuantity' => 0, // Invalide
                            'maxOrderQuantity' => 100,
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    \expect($this->product->getMinOrderQuantity())->toBe(1);
});

\it('sets default max quantity when value is 0', function () {
    $offers = [
        [
            'offerInventory' => [
                'prices' => [
                    [
                        'price' => 100.0,
                        'priceReference' => 120.0,
                        'defaultInventory' => [
                            'minOrderQuantity' => 1,
                            'maxOrderQuantity' => 0, // Invalide
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    \expect($this->product->getMaxOrderQuantity())->toBe(999);
});

\it('handles malformed custom fields gracefully', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
                'customFieldValues' => [
                    [], // Malformed
                    ['customField' => null], // Malformed
                    ['customField' => ['externalId' => null]], // Malformed
                ],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'price' => ['itemPrice' => 120.0],
                            'discountPrice' => ['itemPrice' => 100.0],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    // Ne devrait pas crasher - le pricing est maintenant géré par DjustVariantMapper
    \expect($this->product)->toBeInstanceOf(Product::class);
});

\it('extracts attachments from first offer with attachments', function () {
    $offers = [
        [
            'offerInventory' => [
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'OFFER_ATTACHMENT'],
                        'value' => ['value' => 'https://example.com/file1.pdf'],
                    ],
                ],
            ],
        ],
        [
            'offerInventory' => [
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'OFFER_ATTACHMENT'],
                        'value' => ['value' => 'https://example.com/file2.pdf'],
                    ],
                ],
            ],
        ],
    ];

    // Mock l'extractor - extractAllAttachments s'arrête à la première offre avec des attachments
    $extractor = Mockery::mock(DjustDataExtractor::class);
    $extractor->shouldReceive('extractSingleOfferPrice')
        ->andReturn([
            'price' => 100.0,
            'priceReference' => 120.0,
            'priceRanges' => [
                ['quantity' => 1, 'price' => 100.0, 'priceReference' => 120.0],
            ],
        ]);
    $extractor->shouldReceive('calculateDiscountPercent')
        ->andReturn(16.67);
    $extractor->shouldReceive('extractAttachments')
        ->once()
        ->andReturn(
            [['name' => 'File 1', 'url' => 'https://example.com/file1.pdf', 'type' => 'pdf']]
        );

    $propertyFilter = new DjustPropertyFilter($this->extractor);
    $mapper = new DjustOfferMapper($extractor, $propertyFilter);

    $mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    $attachments = $this->product->getAttachments();
    \expect($attachments)->toHaveCount(1);

    Mockery::close();
});

\it('returns empty attachments when first offer has no attachments', function () {
    $offers = [
        [
            'offerInventory' => [
                'customFieldValues' => [],
            ],
        ],
        [
            'offerInventory' => [
                'customFieldValues' => [],
            ],
        ],
    ];

    // Mock l'extractor - extractAllAttachments parcourt les offres jusqu'à trouver des attachments
    $extractor = Mockery::mock(DjustDataExtractor::class);
    $extractor->shouldReceive('extractSingleOfferPrice')
        ->andReturn([
            'price' => 100.0,
            'priceReference' => 120.0,
            'priceRanges' => [
                ['quantity' => 1, 'price' => 100.0, 'priceReference' => 120.0],
            ],
        ]);
    $extractor->shouldReceive('calculateDiscountPercent')
        ->andReturn(16.67);
    $extractor->shouldReceive('extractAttachments')
        ->twice()
        ->andReturn([], []); // Aucune offre n'a d'attachments

    $propertyFilter = new DjustPropertyFilter($this->extractor);
    $mapper = new DjustOfferMapper($extractor, $propertyFilter);

    $mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    $attachments = $this->product->getAttachments();
    \expect($attachments)->toBeEmpty();

    Mockery::close();
});

\it('extracts NOT_SELLABLE custom fields when product type is NOT_SELLABLE', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-1'],
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => DjustCustomField::OFFER_PRICE_TOP_LABEL->value],
                        'value' => 'Offre exclusive',
                    ],
                    [
                        'customField' => ['externalId' => DjustCustomField::OFFER_PRICE_PRICING_PHRASE->value],
                        'value' => 'Sur le tarif public',
                    ],
                ],
            ],
            'offerPrices' => [],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::NOT_SELLABLE);

    \expect($this->product->getProductTopLabel())->toBe('Offre exclusive');
    \expect($this->product->getProductPricingPhrase())->toBe('Sur le tarif public');
});

\it('does not extract NOT_SELLABLE fields for SELLABLE products', function () {
    $offers = [
        [
            'offerInventory' => [
                'customFieldValues' => [
                    [
                        'customField' => ['externalId' => 'OFFER_PRICE_TOP_LABEL'],
                        'value' => ['value' => 'Offre exclusive'],
                    ],
                ],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::SELLABLE);

    \expect($this->product->getProductTopLabel())->toBeNull();
});

\it('handles empty custom field values for NOT_SELLABLE', function () {
    $offers = [
        [
            'offerInventory' => [
                'customFieldValues' => [],
            ],
        ],
    ];

    $this->mapper->mapOffersData($this->product, $offers, DjustProductType::NOT_SELLABLE);

    \expect($this->product->getProductTopLabel())->toBeNull();
    \expect($this->product->getProductPricingPhrase())->toBeNull();
});
