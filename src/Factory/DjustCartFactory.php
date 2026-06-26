<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Cart;
use App\Mapper\Cart\DjustCartMapper;
use App\Service\Djust\DjustProductService;

class DjustCartFactory
{
    public function __construct(
        private readonly DjustCartMapper $cartMapper,
        private readonly DjustProductService $djustProductService,
    ) {
    }

    public function createFromCommercialOrder(array $djustCart): Cart
    {
        $enrichedCart = $this->enrichWithOfferPrices($djustCart);

        return $this->cartMapper->mapCommercialOrderToCart($enrichedCart);
    }

    private function enrichWithOfferPrices(array $cart): array
    {
        $orderLogistics = $cart['orderLogistics'] ?? [];

        foreach ($orderLogistics as &$order) {
            $lines = $order['lines'] ?? [];

            foreach ($lines as &$item) {
                $offerInventoryId = $item['offerInventorySnapshotDto']['offerInventoryExternalId'] ?? null;

                if ($offerInventoryId) {
                    $item['offerPrices'] = $this->djustProductService->getOffersByOfferInventory($offerInventoryId);
                }
            }

            $order['lines'] = $lines;
        }

        $cart['orderLogistics'] = $orderLogistics;

        return $cart;
    }
}
