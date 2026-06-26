<?php

declare(strict_types=1);

use App\Enum\Djust\DjustCustomField;
use App\Service\Djust\DjustDataExtractor;

\uses()->group('UnitDjustDataExtractor');

\beforeEach(function () {
    $this->extractor = new DjustDataExtractor();
});

\it('extracts localized value from fr-FR locale', function () {
    $data = ['fr-FR' => 'Valeur française', 'en-US' => 'English value'];

    $result = $this->extractor->getLocalizedValue($data);

    \expect($result)->toBe('Valeur française');
});

\it('returns first available value when exact locale not found', function () {
    $data = ['en-US' => 'English value', 'de-DE' => 'German value'];

    $result = $this->extractor->getLocalizedValue($data);

    \expect($result)->toBe('English value');
});

\it('returns string value when data is already a string', function () {
    $data = 'Simple string';

    $result = $this->extractor->getLocalizedValue($data);

    \expect($result)->toBe('Simple string');
});

\it('returns empty string when data is null', function () {
    $result = $this->extractor->getLocalizedValue(null);

    \expect($result)->toBe('');
});

\it('extracts custom field value', function () {
    $customFieldValues = [
        [
            'customField' => [
                'externalId' => 'PRODUCT_SHIPPING_CATEGORY',
                'type' => 'TEXT',
            ],
            'value' => 'COMEBACK18',
        ],
    ];

    $result = $this->extractor->extractCustomFieldValue($customFieldValues, DjustCustomField::PRODUCT_SHIPPING_CATEGORY);

    \expect($result)->toBe('COMEBACK18');
});

\it('returns null when custom field not found', function () {
    $customFieldValues = [
        [
            'customField' => [
                'externalId' => 'OTHER_FIELD',
                'type' => 'TEXT',
            ],
            'value' => 'value',
        ],
    ];

    $result = $this->extractor->extractCustomFieldValue($customFieldValues, DjustCustomField::PRODUCT_SHIPPING_CATEGORY);

    \expect($result)->toBeNull();
});

\it('extracts attachments with new nested value format (OFFER_ATTACHMENT)', function () {
    $customFieldValues = [
        [
            'customField' => [
                'id' => '0000002817',
                'externalId' => 'OFFER_ATTACHMENT',
                'type' => 'MEDIA',
                'name' => ['fr-FR' => 'Pièce jointe'],
            ],
            'value' => [
                'customField' => [
                    'id' => '0000002817',
                    'name' => ['fr-FR' => 'Pièce jointe'],
                    'externalId' => 'OFFER_ATTACHMENT',
                    'type' => 'MEDIA',
                ],
                'value' => 'https://document.pre-prod.djust-app.com/qantis/Invoice-12345.pdf',
                'type' => 'MEDIA',
            ],
        ],
    ];

    $result = $this->extractor->extractAttachments($customFieldValues);

    \expect($result)->toHaveCount(1);
    \expect($result[0])->toHaveKeys(['name', 'url', 'type']);
    \expect($result[0]['name'])->toBe('Pièce jointe');
    \expect($result[0]['url'])->toBe('https://document.pre-prod.djust-app.com/qantis/Invoice-12345.pdf');
    \expect($result[0]['type'])->toBe('pdf');
});

\it('extracts price from single offer', function () {
    $jsonPath = __DIR__.'/../../../Api/_data/_mocks/djust-response/products/offers.json';
    $offersResponse = \json_decode(\file_get_contents($jsonPath), true);
    $offers = $offersResponse['content'] ?? [];
    $singleOffer = $offers[0] ?? [];

    $result = $this->extractor->extractSingleOfferPrice($singleOffer);

    \expect($result)->toBeArray();
    \expect($result)->toHaveKeys(['price', 'priceReference', 'variantId', 'defaultInventory', 'priceRanges']);
    // Le mock contient maintenant des prix dégressifs, le prix par défaut est pour quantité 1
    \expect($result['price'])->toBe(18.0);
    \expect($result['priceReference'])->toBe(20.0);
    \expect($result['priceRanges'])->toBeArray();
    // Vérifier que le tableau contient bien les 4 tranches
    \expect($result['priceRanges'])->toHaveCount(4);
    \expect($result['priceRanges'][0])->toEqual([
        'quantity' => 1,
        'price' => 18.0,
        'priceReference' => 20.0,
    ]);
});

\it('calculates discount percent correctly', function () {
    $priceReference = 100;
    $price = 80;

    $result = $this->extractor->calculateDiscountPercent($priceReference, $price);

    \expect($result)->toBe(20.0);
});

\it('returns 0 discount when no reference price', function () {
    $priceReference = 0;
    $price = 80;

    $result = $this->extractor->calculateDiscountPercent($priceReference, $price);

    \expect($result)->toBe(0.0);
});

\it('extracts images from product data', function () {
    $jsonPath = __DIR__.'/../../../Api/_data/_mocks/djust-response/products/product.json';
    $productData = \json_decode(\file_get_contents($jsonPath), true);

    $result = $this->extractor->extractImages($productData);

    \expect($result)->toBeArray();
});

