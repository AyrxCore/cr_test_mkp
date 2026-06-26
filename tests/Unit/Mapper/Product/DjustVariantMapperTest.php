<?php

declare(strict_types=1);

use App\Dto\Product;
use App\Mapper\Product\DjustVariantMapper;
use App\Service\Djust\DjustDataExtractor;
use Psr\Log\LoggerInterface;

\uses()->group('UnitDjustVariantMapper');

\beforeEach(function () {
    $this->extractor = new DjustDataExtractor();
    $logger = Mockery::mock(LoggerInterface::class)->shouldIgnoreMissing();
    $this->mapper = new DjustVariantMapper($this->extractor, $logger);
    $this->product = new Product();
});

\it('throws exception when variantId is missing', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => [],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            // Pas de variant avec id
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);
})->throws(RuntimeException::class, 'Aucun variant ID trouvé dans les données Djust');

\it('throws exception when offer is empty', function () {
    $offers = [[]];
    $singleOffer = [];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);
})->throws(RuntimeException::class, 'Aucun variant ID trouvé dans les données Djust');

\it('throws exception when variantId is empty string', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => ''],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => ''],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);
})->throws(RuntimeException::class);

\it('handles empty offers array', function () {
    $offers = [];
    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => 'VAR-123'],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getDefaultVariantId())->toBe('VAR-123');
    \expect($this->product->getVariants())->toBeArray()->toBeEmpty();
    \expect($this->product->getOptions())->toBeArray()->toBeEmpty();
});

\it('handles malformed offer structure', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-123',
                    // Structure incomplète mais avec id
                ],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => 'VAR-123'],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getDefaultVariantId())->toBe('VAR-123');
    \expect($this->product->getVariants())->toHaveCount(1);
    \expect($this->product->getVariants()[0]->getId())->toBe('VAR-123');
});

\it('skips attributes with empty names', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-123',
                    'attributeValues' => [
                        [
                            'attribute' => [
                                'id' => '1',
                                'names' => [], // Nom vide
                                'externalId' => 'some_field',
                            ],
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => 'VAR-123'],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getOptions())->toBeArray()->toBeEmpty();
});

\it('skips product_accord_id attribute', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-123',
                    'attributeValues' => [
                        [
                            'attribute' => [
                                'id' => '1',
                                'names' => ['fr-FR' => 'Accord'],
                                'externalId' => 'product_accord_id',
                            ],
                            'value' => 'ACCORD-123',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => 'VAR-123'],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getOptions())->toBeArray()->toBeEmpty();
});

\it('maps variants correctly with valid data', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-123',
                    'skuVariant' => 'SKU-123',
                    'attributeValues' => [
                        [
                            'attribute' => [
                                'id' => '1',
                                'name' => ['fr-FR' => 'Couleur', 'FR' => 'Couleur'],
                                'externalId' => 'color',
                            ],
                            'value' => 'Rouge',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = [
        'offerInventory' => [
            'variant' => ['id' => 'VAR-123'],
        ],
    ];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getDefaultVariantId())->toBe('VAR-123');
    \expect($this->product->getVariants())->toHaveCount(1);
    \expect($this->product->getOptions())->toHaveKey('Couleur');
});

\it('keeps only the first offer when multiple offers have the same variant', function () {
    $offers = [
        [
            'offerInventory' => [
                'id' => 'OFFER-1',
                'variant' => [
                    'id' => 'VAR-123',
                    'skuVariant' => 'SKU-001',
                    'attributeValues' => [
                        [
                            'attribute' => ['name' => ['fr' => 'Taille'], 'id' => 'ATTR-1'],
                            'value' => 'medium',
                        ],
                    ],
                ],
            ],
            'offerPrices' => [
                [
                    'externalId' => 'PRICE-1',
                    'priceRanges' => [
                        ['quantity' => 1, 'price' => ['itemPrice' => 100]],
                    ],
                ],
            ],
        ],
        [
            'offerInventory' => [
                'id' => 'OFFER-2',
                'variant' => [
                    'id' => 'VAR-123', // Même variant ID
                    'skuVariant' => 'SKU-001',
                    'attributeValues' => [
                        [
                            'attribute' => ['name' => ['fr' => 'Taille'], 'id' => 'ATTR-1'],
                            'value' => 'medium',
                        ],
                    ],
                ],
            ],
            'offerPrices' => [
                [
                    'externalId' => 'PRICE-2',
                    'priceRanges' => [
                        ['quantity' => 1, 'price' => ['itemPrice' => 120]],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = $offers[0];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    \expect($this->product->getVariants())->toHaveCount(1);
    \expect($this->product->getVariants()[0]->getId())->toBe('VAR-123');
    \expect($this->product->getVariants()[0]->getOfferPriceExternalId())->toBe('PRICE-1');
});

\it('throws exception when an offer in the array is missing variant id', function () {
    $offers = [
        [
            'offerInventory' => [
                'id' => 'OFFER-1',
                'variant' => [
                    'id' => 'VAR-123',
                    'skuVariant' => 'SKU-001',
                    'attributeValues' => [
                        [
                            'attribute' => ['name' => ['fr' => 'Taille'], 'id' => 'ATTR-1'],
                            'value' => 'medium',
                        ],
                    ],
                ],
            ],
            'offerPrices' => [
                [
                    'externalId' => 'PRICE-1',
                    'priceRanges' => [
                        ['quantity' => 1, 'price' => ['itemPrice' => 100]],
                    ],
                ],
            ],
        ],
        [
            'offerInventory' => [
                'id' => 'OFFER-2',
                'variant' => [], // Pas de variant ID
            ],
            'offerPrices' => [
                [
                    'externalId' => 'PRICE-2',
                    'priceRanges' => [
                        ['quantity' => 1, 'price' => ['itemPrice' => 120]],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = $offers[0];

    \expect(fn () => $this->mapper->mapVariants($this->product, $offers, $singleOffer))
        ->toThrow(RuntimeException::class, 'Variant ID is required to ensure uniqueness of offers per variant');
});

\it('sets correct default prices from default variant', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-123'],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'quantity' => 1,
                            'price' => ['itemPrice' => 100], // Prix de référence
                            'discountPrice' => ['itemPrice' => 80], // Prix avec remise
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = $offers[0];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    // Vérifications des prix par défaut
    \expect($this->product->getPrice())->toBe(80.0); // Prix avec remise
    \expect($this->product->getPriceReference())->toBe(100.0); // Prix de référence
    \expect($this->product->getPercent())->toBe(20.0); // Pourcentage de remise calculé

    // Vérification que le variant a aussi le même percent
    $variants = $this->product->getVariants();
    \expect($variants[0]->getPercent())->toBe(20.0);

    // Vérification que les priceRanges sont bien propagés
    \expect($variants[0]->getPriceRanges())->toBeArray();
    \expect($variants[0]->getPriceRanges())->toHaveCount(1);
    \expect($variants[0]->getPriceRanges()[0])->toEqual([
        'quantity' => 1,
        'price' => 80.0,
        'priceReference' => 100.0,
    ]);

    // Le produit doit avoir les mêmes priceRanges que le variant par défaut
    \expect($this->product->getPriceRanges())->toBe($variants[0]->getPriceRanges());
});

\it('product percent matches default variant percent', function () {
    // Test avec plusieurs variants pour s'assurer que le percent du produit
    // correspond bien au percent du variant par défaut
    $offers = [
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-1',
                    'attributeValues' => [
                        ['attribute' => ['name' => ['fr' => 'Taille']], 'value' => 'S'],
                    ],
                ],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'quantity' => 1,
                            'price' => ['itemPrice' => 100],
                            'discountPrice' => ['itemPrice' => 90], // 10% remise
                        ],
                    ],
                ],
            ],
        ],
        [
            'offerInventory' => [
                'variant' => [
                    'id' => 'VAR-2', // Variant par défaut (premier dans la liste)
                    'attributeValues' => [
                        ['attribute' => ['name' => ['fr' => 'Taille']], 'value' => 'M'],
                    ],
                ],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'quantity' => 1,
                            'price' => ['itemPrice' => 100],
                            'discountPrice' => ['itemPrice' => 75], // 25% remise
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = $offers[1]; // VAR-2 est le variant par défaut

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    // Le produit doit avoir le même percent que le variant par défaut (VAR-2)
    $variants = $this->product->getVariants();
    $defaultVariantPercent = null;

    foreach ($variants as $variant) {
        if ($variant->getId() === $this->product->getDefaultVariantId()) {
            $defaultVariantPercent = $variant->getPercent();
            break;
        }
    }

    \expect($this->product->getPercent())->toBe($defaultVariantPercent);
    \expect($this->product->getPercent())->toBe(25.0); // 25% pour VAR-2

    // Le produit doit avoir les priceRanges du variant par défaut
    $defaultVariant = $variants[array_search('VAR-2', array_map(fn($v) => $v->getId(), $variants))];
    \expect($this->product->getPriceRanges())->toBe($defaultVariant->getPriceRanges());
});

\it('handles multiple price ranges for quantity discounts', function () {
    $offers = [
        [
            'offerInventory' => [
                'variant' => ['id' => 'VAR-123'],
            ],
            'offerPrices' => [
                [
                    'priceRanges' => [
                        [
                            'quantity' => 1,
                            'price' => ['itemPrice' => 20],
                            'discountPrice' => ['itemPrice' => 18],
                        ],
                        [
                            'quantity' => 2,
                            'price' => ['itemPrice' => 20],
                            'discountPrice' => ['itemPrice' => 14],
                        ],
                        [
                            'quantity' => 4,
                            'price' => ['itemPrice' => 20],
                            'discountPrice' => ['itemPrice' => 13],
                        ],
                        [
                            'quantity' => 8,
                            'price' => ['itemPrice' => 20],
                            'discountPrice' => ['itemPrice' => 12],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $singleOffer = $offers[0];

    $this->mapper->mapVariants($this->product, $offers, $singleOffer);

    $variants = $this->product->getVariants();

    // Le variant doit avoir 4 tranches de prix
    \expect($variants[0]->getPriceRanges())->toHaveCount(4);

    // Le prix par défaut doit être celui de la quantité 1
    \expect($this->product->getPrice())->toBe(18.0);
    \expect($this->product->getPriceReference())->toBe(20.0);

    // Vérifier que les tranches sont bien triées par quantité
    $priceRanges = $variants[0]->getPriceRanges();
    \expect($priceRanges[0]['quantity'])->toBe(1);
    \expect($priceRanges[0]['price'])->toBe(18.0);
    \expect($priceRanges[1]['quantity'])->toBe(2);
    \expect($priceRanges[1]['price'])->toBe(14.0);
    \expect($priceRanges[2]['quantity'])->toBe(4);
    \expect($priceRanges[2]['price'])->toBe(13.0);
    \expect($priceRanges[3]['quantity'])->toBe(8);
    \expect($priceRanges[3]['price'])->toBe(12.0);

    // Le produit doit avoir les mêmes priceRanges
    \expect($this->product->getPriceRanges())->toBe($variants[0]->getPriceRanges());
});
