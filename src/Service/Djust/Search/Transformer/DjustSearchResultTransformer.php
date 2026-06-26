<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchResultTransformer
{
    public function __construct(
        private readonly DjustSearchProductTransformer $productTransformer,
        private readonly DjustSearchOfferTransformer $offerTransformer,
        private readonly DjustSearchOfferPriceTransformer $offerPriceTransformer,
        private readonly DjustSearchSupplierTransformer $supplierTransformer,
    ) {
    }

    public function transformSearchResultItem(array $searchItem): array
    {
        $supplier = $searchItem['supplier'] ?? [];
        $offerPrice = $searchItem['offerPrice'] ?? [];

        return [
            'product' => $this->productTransformer->transform($searchItem),
            'offers' => [[
                'offerInventory' => $this->offerTransformer->transform($searchItem),
                'supplier' => $this->supplierTransformer->transform($supplier),
                'offerPrices' => $this->offerPriceTransformer->transform($offerPrice),
            ]],
        ];
    }
}
