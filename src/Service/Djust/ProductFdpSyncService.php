<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Dto\Cart;
use App\Dto\CartItem;
use App\Dto\Product;
use App\Enum\Djust\DjustCartItemAction;
use Psr\Log\LoggerInterface;

use function Sentry\captureMessage;

use Sentry\State\Scope;

use function Sentry\withScope;

class ProductFdpSyncService
{
    public const string EXTERNAL_ID_PREFIX = 'PRODUCT_FDP_';
    private const float UNIT_PRICE = 0.1;

    public function __construct(
        private readonly DjustCartService $djustCartService,
        private readonly DjustProductService $djustProductService,
        private readonly LoggerInterface $djustLogger,
    ) {
    }

    public function syncForCart(string $cartId, Cart $cart): void
    {
        $lines = $this->buildUpdateLines($cart);

        if (empty($lines)) {
            return;
        }

        $this->djustCartService->updateCartItems($cartId, $lines);
    }

    /**
     * @return CartItem[]
     */
    private function buildUpdateLines(Cart $cart): array
    {
        $lines = [];

        foreach ($cart->getCartOrders() as $cartOrder) {
            $products = $cartOrder->getProducts();
            $productsFdp = \array_values(\array_filter($products, $this->isProductFdp(...)));
            $realProducts = \array_values(\array_filter($products, fn (Product $p) => !$this->isProductFdp($p)));

            foreach ($realProducts as $product) {
                $offerPriceId = $product->getVariants()[0]?->getOfferPriceExternalId();
                if ($offerPriceId !== null) {
                    $lines[] = $this->buildCartItem($offerPriceId, $product->getQuantity() ?? 1);
                }
            }

            $existingProductFdp = $productsFdp[0] ?? null;

            if (empty($realProducts)) {
                if ($existingProductFdp !== null) {
                    $offerPriceId = $existingProductFdp->getVariants()[0]?->getOfferPriceExternalId();
                    if ($offerPriceId !== null) {
                        $lines[] = $this->buildCartItem($offerPriceId, 0);
                    }
                }
                continue;
            }

            $fdp = $cartOrder->getShippingCostResult()?->shippingCost ?? 0.0;

            if ($fdp > 0) {
                $offerPriceId = $existingProductFdp?->getVariants()[0]?->getOfferPriceExternalId()
                    ?? $this->fetchOfferPriceId($cartOrder->getSeller()->getExternalId());

                if ($offerPriceId === null) {
                    continue;
                }

                $quantity = (int) \round($fdp / self::UNIT_PRICE);
                $lines[] = $this->buildCartItem($offerPriceId, $quantity);
            } elseif ($existingProductFdp !== null) {
                $offerPriceId = $existingProductFdp->getVariants()[0]?->getOfferPriceExternalId();
                if ($offerPriceId !== null) {
                    $lines[] = $this->buildCartItem($offerPriceId, 0);
                }
            }
        }

        return $lines;
    }

    private function isProductFdp(Product $product): bool
    {
        return \str_starts_with($product->getExternalId() ?? '', self::EXTERNAL_ID_PREFIX);
    }

    private function fetchOfferPriceId(string $supplierId): ?string
    {
        $externalId = self::EXTERNAL_ID_PREFIX.$supplierId;

        try {
            $offers = $this->djustProductService->getProductOffers($externalId);
            $offerPriceId = $offers[0]['offerPrices'][0]['externalId'] ?? null;
        } catch (\Throwable $e) {
            $offerPriceId = null;
            $this->djustLogger->error('Exception lors de la récupération du product FDP', [
                'supplier_id' => $supplierId,
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);
        }

        if ($offerPriceId === null) {
            $this->djustLogger->warning('Aucun offerPriceId trouvé pour le product FDP du fournisseur : les FDP seront considérées comme gratuites', [
                'supplier_id' => $supplierId,
                'external_id' => $externalId,
            ]);

            withScope(static function (Scope $scope) use ($supplierId, $externalId): void {
                $scope->setTag('supplier_id', $supplierId);
                $scope->setTag('issue_type', 'missing_product_fdp');
                $scope->setContext('product_fdp', [
                    'supplier_id' => $supplierId,
                    'external_id' => $externalId,
                ]);
                captureMessage(\sprintf(
                    'Product FDP manquant pour le fournisseur %s (externalId: %s) : les FDP de ce fournisseur sont considérées comme gratuites',
                    $supplierId,
                    $externalId
                ));
            });

            return null;
        }

        return $offerPriceId;
    }

    private function buildCartItem(string $offerPriceId, int $quantity): CartItem
    {
        $item = new CartItem();
        $item->setOfferPriceId($offerPriceId)
            ->setQuantity($quantity)
            ->setAction(DjustCartItemAction::REPLACE_QUANTITY->value);

        return $item;
    }
}
