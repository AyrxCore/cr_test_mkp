<?php

declare(strict_types=1);

namespace App\Mapper\Cart;

use App\Dto\Cart;
use App\Dto\CartOrder;
use App\Dto\Product;
use App\Enum\Djust\DjustCustomField;
use App\Factory\DjustProductFactory;
use App\Service\Djust\Cart\Transformer\DjustCartItemTransformer;
use App\Factory\SellerFactory;
use App\Service\Djust\ProductFdpSyncService;
use App\Service\Shipping\ShippingCostService;

class DjustCartMapper
{
    public function __construct(
        private readonly SellerFactory $sellerFactory,
        private readonly DjustProductFactory $djustProductFactory,
        private readonly DjustCartItemTransformer $djustCartItemTransformer,
        private readonly ShippingCostService $shippingCostService,
    ) {
    }

    public function mapCommercialOrderToCart(array $djustCart): Cart
    {
        $cart = new Cart();
        $cart->setId($djustCart['reference'] ?? null);
        $cart->setCurrency(($djustCart['orderLogisticPrices'] ?? [])['currency'] ?? null);

        foreach ($djustCart['orderLogistics'] ?? [] as $order) {
            $cart->addCartOrder($this->mapOrderLogistic($order));
        }

        $correctedHT = array_sum(array_map(static fn(CartOrder $o) => $o->getTotalPrice() ?? 0.0, $cart->getCartOrders()));
        $correctedTTC = array_sum(array_map(static fn(CartOrder $o) => $o->getTotalPriceWithTax() ?? 0.0, $cart->getCartOrders()));
        $cart->setTotalPrice($correctedHT);
        $cart->setTotalPriceWithTax($correctedTTC);

        $realProductCount = 0;
        foreach ($cart->getCartOrders() as $cartOrder) {
            foreach ($cartOrder->getProducts() as $product) {
                if (!str_starts_with($product->getExternalId() ?? '', ProductFdpSyncService::EXTERNAL_ID_PREFIX)) {
                    $realProductCount += $product->getQuantity() ?? 0;
                }
            }
        }
        $cart->setProductCount($realProductCount);

        return $cart;
    }

    private function mapOrderLogistic(array $order): CartOrder
    {
        $cartOrder = new CartOrder();

        $supplierSnapshot = $order['supplierSnapshot'] ?? [];
        $orderLogisticPrices = $order['orderLogisticPrices'] ?? [];

        $seller = $this->sellerFactory->create($supplierSnapshot);

        $cartOrder
            ->setSeller($seller)
            ->setTotalPrice($orderLogisticPrices['totalPriceWithoutTax'] ?? null)
            ->setTotalPriceWithTax($orderLogisticPrices['totalPriceWithTax'] ?? null);

        $items = $order['lines'] ?? [];
        foreach ($items as $item) {
            $product = $this->mapCartItem($item, $supplierSnapshot);
            $cartOrder->addProduct($product);
        }

        $this->subtractProductsFdpTotals($cartOrder, $items);

        $partnerId = $supplierSnapshot['externalId'] ?? null;
        if ($partnerId !== null) {
            $realProducts = array_values(array_filter(
                $cartOrder->getProducts(),
                static fn(Product $p) => !str_starts_with($p->getExternalId() ?? '', ProductFdpSyncService::EXTERNAL_ID_PREFIX),
            ));
            $productsForShipping = array_map(
                static fn (Product $p) => [
                    'unitPrice' => $p->getPrice() ?? 0.0,
                    'quantity' => $p->getQuantity() ?? 1,
                    'shippingCategory' => $p->getShippingCategory(),
                    'weight' => $p->getWeight(),
                ],
                $realProducts,
            );
            $shippingResult = $this->shippingCostService->computeForPartnerDjustId($partnerId, $productsForShipping);
            if ($shippingResult !== null) {
                $cartOrder->setShippingCostResult(
                    $shippingResult->withMaxTaxRate($this->extractMaxTaxRate($items)),
                );
            }
        }

        return $cartOrder;
    }

    private function subtractProductsFdpTotals(CartOrder $cartOrder, array $rawItems): void
    {
        $fdpTotalHT = 0.0;
        $fdpTotalTTC = 0.0;

        foreach ($rawItems as $item) {
            $productExternalId = $item['orderLogisticLineProductDto']['externalId'] ?? null;
            if (!str_starts_with($productExternalId ?? '', ProductFdpSyncService::EXTERNAL_ID_PREFIX)) {
                continue;
            }

            $priceSnapshot = $item['offerPriceSnapshotDto'] ?? [];
            $qty = (int) ($item['quantity'] ?? 0);
            $fdpTotalHT += ($priceSnapshot['productPriceWithoutTaxes'] ?? 0.0) * $qty;
            $fdpTotalTTC += ($priceSnapshot['productPriceWithTaxes'] ?? 0.0) * $qty;
        }

        $cartOrder->setTotalPrice(max(0.0, ($cartOrder->getTotalPrice() ?? 0.0) - $fdpTotalHT));
        $cartOrder->setTotalPriceWithTax(max(0.0, ($cartOrder->getTotalPriceWithTax() ?? 0.0) - $fdpTotalTTC));
    }

    private function extractMaxTaxRate(array $items): float
    {
        $maxRate = 0.0;

        foreach ($items as $item) {
            $productExternalId = $item['orderLogisticLineProductDto']['externalId'] ?? null;
            if (str_starts_with($productExternalId ?? '', ProductFdpSyncService::EXTERNAL_ID_PREFIX)) {
                continue;
            }

            $customFields = $item['offerInventorySnapshotDto']['customFieldValueSnapshots'] ?? [];
            foreach ($customFields as $cf) {
                $externalId = $cf['customFieldSnapshotDto']['externalId'] ?? null;
                if ($externalId === 'Offer_Tax_Rate') {
                    $typedValue = $cf['typedValue'] ?? [];
                    $rate = is_array($typedValue) ? ($typedValue[0] ?? 0.0) : (float) $typedValue;
                    $maxRate = max($maxRate, (float) $rate);
                    break;
                }
            }
        }

        return $maxRate;
    }

    private function extractWeightFromSnapshot(array $item): ?float
    {
        $customFields = $item['offerInventorySnapshotDto']['customFieldValueSnapshots'] ?? [];
        foreach ($customFields as $cf) {
            $externalId = $cf['customFieldSnapshotDto']['externalId'] ?? null;
            if (strtolower((string) $externalId) === strtolower(DjustCustomField::PRODUCT_WEIGHT->value)) {
                $typedValue = $cf['typedValue'] ?? $cf['value'] ?? null;
                $value = is_array($typedValue) ? ($typedValue[0] ?? null) : $typedValue;

                return $value !== null ? (float) $value : null;
            }
        }

        return null;
    }

    private function mapCartItem(array $item, array $supplier): Product
    {
        $data = $this->djustCartItemTransformer->transform($item, $supplier);
        $offerPrices = $item['offerPrices'] ?? [];
        $itemPrice = $item['offerPriceSnapshotDto']['productPriceWithoutTaxes'] ?? null;

        if ($itemPrice !== null && isset($offerPrices['content'][0]['priceRanges'])) {
            foreach ($offerPrices['content'][0]['priceRanges'] as $range) {
                $discountPrice = $range['discountPrice']['itemPrice'] ?? null;
                $unitPrice = $range['price']['unitPrice'] ?? null;

                if (intval($discountPrice) === intval($itemPrice)) {
                    $data['offers'][0]['offerPrices'][0]['priceRanges'][0]['price']['itemPrice'] = $unitPrice;
                    $data['offers'][0]['offerPrices'][0]['priceRanges'][0]['price']['unitPrice'] = $unitPrice;
                    $data['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice']['itemPrice'] = $itemPrice;
                    $data['offers'][0]['offerPrices'][0]['priceRanges'][0]['discountPrice']['unitPrice'] = $itemPrice;
                    break;
                }
            }
        }

        $product = $this->djustProductFactory->create($data);

        if ($product->getWeight() === null) {
            $product->setWeight($this->extractWeightFromSnapshot($item));
        }

        return $product;
    }
}
