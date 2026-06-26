<?php

declare(strict_types=1);

namespace App\Service\Djust\Cart\Transformer;

use App\Service\Djust\Search\Transformer\DjustSearchSupplierTransformer;

class DjustCartItemTransformer
{
    public function __construct(
        private readonly DjustCartProductTransformer $productTransformer,
        private readonly DjustCartOfferTransformer $offerTransformer,
        private readonly DjustCartOfferPriceTransformer $offerPriceTransformer,
        private readonly DjustSearchSupplierTransformer $supplierTransformer,
    ) {
    }

    public function transform(array $cartItem, array $supplier): array
    {
        return [
            'product' => $this->productTransformer->transform($cartItem),
            'offers' => [[
                'offerInventory' => $this->offerTransformer->transform($cartItem),
                'supplier' => $this->supplierTransformer->transform($supplier),
                'offerPrices' => $this->offerPriceTransformer->transform($cartItem),
            ]],
        ];
    }
}
