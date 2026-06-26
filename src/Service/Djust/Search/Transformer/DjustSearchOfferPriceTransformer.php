<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchOfferPriceTransformer
{
    public function transform(array $offerPrice): array
    {
        if (empty($offerPrice)) {
            return [];
        }

        return [[
            'id' => $offerPrice['id'] ?? null,
            'externalId' => $offerPrice['externalId'] ?? null,
            'offerPriceType' => $offerPrice['offerPriceType'] ?? 'GROUP',
            'itemPerPack' => $offerPrice['itemPerPack'] ?? 1,
            'customerAccountId' => null,
            'customerTagId' => null,
            'priceRanges' => [
                [
                    'quantity' => 1,
                    'price' => [
                        'itemPrice' => $offerPrice['itemPrice'] ?? $offerPrice['price'] ?? 0,
                        'itemPriceTTC' => $offerPrice['unitPriceTTC'] ?? $offerPrice['itemPrice'] ?? 0,
                        'unitPrice' => $offerPrice['unitPrice'] ?? $offerPrice['price'] ?? 0,
                        'unitPriceTTC' => $offerPrice['unitPriceTTC'] ?? 0,
                    ],
                    'discountPrice' => ($offerPrice['discountItemPrice'] || $offerPrice['discountUnitPrice']) ? [
                        'itemPrice' => $offerPrice['discountItemPrice'] ?? null,
                        'itemPriceTTC' => $offerPrice['discountItemPrice'] ?? null,
                        'unitPrice' => $offerPrice['discountUnitPrice'] ?? null,
                        'unitPriceTTC' => $offerPrice['discountUnitPrice'] ?? null,
                    ] : null,
                ],
            ],
        ]];
    }
}
