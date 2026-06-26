<?php

declare(strict_types=1);

namespace App\Service\Djust\Cart\Transformer;

class DjustCartOfferPriceTransformer
{
    public function transform(array $cartItem): array
    {
        $priceSnapshot = $cartItem['offerPriceSnapshotDto'] ?? [];

        if (empty($priceSnapshot)) {
            return [];
        }

        return [[
            'id' => $priceSnapshot['offerPriceExternalId'] ?? null,
            'externalId' => $priceSnapshot['offerPriceExternalId'] ?? null,
            'offerPriceType' => $priceSnapshot['offerPriceType'] ?? 'GROUP',
            'itemPerPack' => $priceSnapshot['itemPerPack'] ?? 1,
            'customerAccountId' => null,
            'customerTagId' => null,
            'priceRanges' => [
                [
                    'quantity' => 1,
                    'price' => [
                        'itemPrice' => $priceSnapshot['productPriceWithoutTaxes'] ?? 0,
                        'itemPriceTTC' => $priceSnapshot['productPriceWithTaxes'] ?? 0,
                        'unitPrice' => $priceSnapshot['productPriceWithoutTaxes'] ?? 0,
                        'unitPriceTTC' => $priceSnapshot['productPriceWithTaxes'] ?? 0,
                    ],
                    'discountPrice' => null,
                ],
            ],
        ]];
    }
}
