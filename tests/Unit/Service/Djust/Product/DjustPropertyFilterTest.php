<?php

declare(strict_types=1);

use App\Dto\Product;
use App\Enum\Djust\DjustProductTag;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\Product\DjustPropertyFilter;

\uses()->group('UnitDjustPropertyFilter', 'Djust');

\beforeEach(function () {
    $this->extractor = new DjustDataExtractor();
    $this->filter = new DjustPropertyFilter($this->extractor);
    $this->product = new Product();
});

\it('maps properties from master product without tags', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Couleur'],
                    'externalId' => 'PRODUCT_COLOR',
                ],
                'value' => 'Rouge',
            ],
        ],
        'tags' => [
            ['name' => 'made_in_france'],
            ['name' => 'achat_durable'],
        ],
        'brand' => 'Adidas',
    ];

    $this->filter->mapProperties($this->product, $masterProduct);

    $properties = $this->product->getProperties();

    \expect($properties)->toHaveKey('Couleur');
    \expect($properties)->toHaveKey('Marque');
    \expect($properties)->not->toHaveKey('tag_promo');
    \expect($properties)->not->toHaveKey('tag_nouveau');
});

\it('returns empty properties when attributeValues is empty and no brand', function () {
    $masterProduct = [
        'attributeValues' => [],
    ];

    $this->filter->mapProperties($this->product, $masterProduct);

    \expect($this->product->getProperties())->toBeArray()->toBeEmpty();
});

\it('handles null brand gracefully', function () {
    $masterProduct = [
        'brand' => null,
        'attributeValues' => [],
        'tags' => [],
    ];

    $this->filter->mapProperties($this->product, $masterProduct);

    $properties = $this->product->getProperties();
    \expect($properties)->not()->toHaveKey('Marque');
});

\it('handles missing brand key gracefully', function () {
    $masterProduct = [
        'attributeValues' => [],
        'tags' => [],
    ];

    $this->filter->mapProperties($this->product, $masterProduct);

    $properties = $this->product->getProperties();
    \expect($properties)->not()->toHaveKey('Marque');
});

\it('detects form with message when value is Oui', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Formulaire avec message'],
                    'externalId' => 'PRODUCT_FORM_WITH_MESSAGE',
                ],
                'value' => ['Oui'],
            ],
        ],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeTrue();
});

\it('detects form with message when value is oui (lowercase)', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Formulaire avec message'],
                    'externalId' => 'PRODUCT_FORM_WITH_MESSAGE',
                ],
                'value' => 'oui',
            ],
        ],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeTrue();
});

\it('returns false when form with message value is Non', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Formulaire avec message'],
                    'externalId' => 'PRODUCT_FORM_WITH_MESSAGE',
                ],
                'value' => ['Non'],
            ],
        ],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeFalse();
});

\it('returns false when form with message is empty', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Formulaire avec message'],
                    'externalId' => 'PRODUCT_FORM_WITH_MESSAGE',
                ],
                'value' => '',
            ],
        ],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeFalse();
});

\it('returns false when form with message attribute is missing', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Autre attribut'],
                    'externalId' => 'OTHER_FIELD',
                ],
                'value' => 'Value',
            ],
        ],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeFalse();
});

\it('returns false when attributeValues is empty', function () {
    $masterProduct = [
        'attributeValues' => [],
    ];

    $result = $this->filter->shouldShowFormWithMessage($masterProduct);

    \expect($result)->toBeFalse();
});

\it('mapTags sets whitelisted tags on product', function () {
    $masterProduct = [
        'tags' => [
            ['name' => 'made_in_france'],
            ['name' => 'achat_durable'],
            ['name' => 'promo'],
        ],
    ];

    $this->filter->mapTags($this->product, $masterProduct);

    \expect($this->product->getTags())->toBe(['made_in_france', 'achat_durable']);
})->group('UnitDjustPropertyFilter', 'Djust');

\it('mapTags returns empty array when no whitelisted tag matches', function () {
    $masterProduct = [
        'tags' => [
            ['name' => 'promo'],
            ['name' => 'nouveau'],
        ],
    ];

    $this->filter->mapTags($this->product, $masterProduct);

    \expect($this->product->getTags())->toBeEmpty();
})->group('UnitDjustPropertyFilter', 'Djust');

\it('mapTags handles empty tags gracefully', function () {
    $masterProduct = ['tags' => []];

    $this->filter->mapTags($this->product, $masterProduct);

    \expect($this->product->getTags())->toBeEmpty();
})->group('UnitDjustPropertyFilter', 'Djust');

\it('mapTags handles missing tags key gracefully', function () {
    $this->filter->mapTags($this->product, []);

    \expect($this->product->getTags())->toBeEmpty();
})->group('UnitDjustPropertyFilter', 'Djust');

\it('isWhitelistedTag returns true for whitelisted tags', function () {
    foreach (DjustProductTag::whitelist() as $tag) {
        \expect($this->filter->isWhitelistedTag($tag))->toBeTrue();
    }
})->group('UnitDjustPropertyFilter', 'Djust');

\it('isWhitelistedTag returns false for unknown tags', function () {
    \expect($this->filter->isWhitelistedTag('promo'))->toBeFalse();
    \expect($this->filter->isWhitelistedTag(''))->toBeFalse();
    \expect($this->filter->isWhitelistedTag('MADE_IN_FRANCE'))->toBeFalse();
})->group('UnitDjustPropertyFilter', 'Djust');

\it('identifies excluded externalIds correctly', function () {
    \expect($this->filter->isExcludedExternalId('PRODUCT_FORM_WITH_MESSAGE'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('PRODUCT_TYPE'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('PRODUCT_ACCORD_ID'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('OFFER_ATTACHMENT'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('OFFER_PRICE_TOP_LABEL'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('OFFER_PRICE_PRICING_PHRASE'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('OFFER_TARIF_ID'))->toBeTrue();
    \expect($this->filter->isExcludedExternalId('CUSTOM_BRAND'))->toBeFalse();
    \expect($this->filter->isExcludedExternalId('PRODUCT_COLOR'))->toBeFalse();
    \expect($this->filter->isExcludedExternalId(''))->toBeFalse();
});

\it('excludes attributes by externalId during mapping', function () {
    $masterProduct = [
        'attributeValues' => [
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Type de produit'],
                    'externalId' => 'PRODUCT_TYPE',
                ],
                'value' => 'SELLABLE',
            ],
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Accord ID'],
                    'externalId' => 'PRODUCT_ACCORD_ID',
                ],
                'value' => '123',
            ],
            [
                'attribute' => [
                    'name' => ['fr-FR' => 'Couleur'],
                    'externalId' => 'PRODUCT_COLOR',
                ],
                'value' => 'Rouge',
            ],
        ],
        'tags' => [],
        'brand' => null,
    ];

    $this->filter->mapProperties($this->product, $masterProduct);

    $properties = $this->product->getProperties();

    \expect($properties)->not()->toHaveKey('Type de produit');
    \expect($properties)->not()->toHaveKey('Accord ID');
    \expect($properties)->toHaveKey('Couleur');
});
