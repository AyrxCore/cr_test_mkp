<?php

declare(strict_types=1);

use App\Dto\Cart;
use App\Dto\CartItem;
use App\Dto\CartOrder;
use App\Dto\Product;
use App\Dto\Seller;
use App\Dto\ShippingCostResult;
use App\Dto\Variant;
use App\Enum\Djust\DjustCartItemAction;
use App\Service\Djust\DjustCartService;
use App\Service\Djust\DjustProductService;
use App\Service\Djust\ProductFdpSyncService;
use Psr\Log\LoggerInterface;

uses()->group('UnitProductFDPSyncService', 'cart', 'fdp');

/**
 * Crée un produit réel (non-FDP) avec un offerPriceExternalId.
 */
function makeRealProduct(string $offerPriceId, int $quantity = 1): Product
{
    $variant = new Variant();
    $variant->setOfferPriceExternalId($offerPriceId);

    $product = new Product();
    $product->setExternalId('REAL_PRODUCT_001')
        ->setQuantity($quantity)
        ->setVariants([$variant]);

    return $product;
}

/**
 * Crée un produit FDP (externalId préfixé par PRODUCT_FDP_).
 */
function makeProductFdpProduct(string $offerPriceId, string $supplierId = 'SUPPLIER-1'): Product
{
    $variant = new Variant();
    $variant->setOfferPriceExternalId($offerPriceId);

    $product = new Product();
    $product->setExternalId(ProductFdpSyncService::EXTERNAL_ID_PREFIX . $supplierId)
        ->setQuantity(1)
        ->setVariants([$variant]);

    return $product;
}

/**
 * Crée un CartOrder avec le seller, les produits et le coût de livraison donnés.
 */
function makeCartOrder(array $products, float $shippingCost, string $sellerExternalId = 'SUPPLIER-1'): CartOrder
{
    $seller = new Seller();
    $seller->setExternalId($sellerExternalId);

    $shippingCostResult = new ShippingCostResult(
        shippingCost: $shippingCost,
        remainingForFranco: 0.0,
        type: 'STANDARD',
    );

    $order = new CartOrder();
    $order->setSeller($seller)
        ->setShippingCostResult($shippingCostResult);

    foreach ($products as $product) {
        $order->addProduct($product);
    }

    return $order;
}

/**
 * Crée un Cart contenant les CartOrders donnés.
 */
function makeCart(array $cartOrders): Cart
{
    $cart = new Cart();
    foreach ($cartOrders as $order) {
        $cart->addCartOrder($order);
    }

    return $cart;
}

beforeEach(function () {
    $this->djustCartService    = Mockery::mock(DjustCartService::class);
    $this->djustProductService = Mockery::mock(DjustProductService::class);
    $this->logger              = Mockery::mock(LoggerInterface::class);
    $this->logger->shouldReceive('warning')->byDefault();
    $this->logger->shouldReceive('error')->byDefault();
    $this->service             = new ProductFdpSyncService(
        $this->djustCartService,
        $this->djustProductService,
        $this->logger,
    );
});

afterEach(function () {
    Mockery::close();
});

it('does nothing if the cart has no orders', function () {
    $this->djustCartService->shouldReceive('updateCartItems')->never();

    $this->service->syncForCart('CART-001', makeCart([]));
});

it('does nothing if the order has no real products and no ProductFDP', function () {
    $order = makeCartOrder([], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')->never();

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('does not create a ProductFDP line if FDP = 0 and no existing ProductFDP', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $order       = makeCartOrder([$realProduct], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter(
                $lines,
                fn (CartItem $l) => str_starts_with($l->getOfferPriceId(), ProductFdpSyncService::EXTERNAL_ID_PREFIX),
            ));

            return count($productFdpLines) === 0 && count($lines) === 1;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('creates a ProductFDP with the correct quantity when FDP > 0 and no existing ProductFDP', function () {
    $realProduct = makeRealProduct('OFFER-001', quantity: 2);
    $order       = makeCartOrder([$realProduct], shippingCost: 15.50, sellerExternalId: 'SUPPLIER-42');

    $this->djustProductService->shouldReceive('getProductOffers')
        ->once()
        ->with(ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-42')
        ->andReturn([[
            'offerPrices' => [['externalId' => 'ProductFDP-OFFER-42']],
        ]]);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-OFFER-42'));
            $productFdpLine  = $productFdpLines[0] ?? null;

            return $cartId === 'CART-001'
                && $productFdpLine !== null
                && $productFdpLine->getQuantity() === 155
                && $productFdpLine->getAction() === DjustCartItemAction::REPLACE_QUANTITY->value;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('reuses the existing ProductFDP instead of fetching it from the API when FDP > 0', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $productFdpProduct = makeProductFdpProduct('ProductFDP-OFFER-EXISTING');
    $order       = makeCartOrder([$realProduct, $productFdpProduct], shippingCost: 10.00);

    $this->djustProductService->shouldReceive('getProductOffers')->never();

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-OFFER-EXISTING'));
            $productFdpLine  = $productFdpLines[0] ?? null;

            return $productFdpLine !== null && $productFdpLine->getQuantity() === 100;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('correctly rounds the ProductFDP quantity', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $order = makeCartOrder([$realProduct], shippingCost: 9.995, sellerExternalId: 'SUPPLIER-1');

    $this->djustProductService->shouldReceive('getProductOffers')
        ->once()
        ->andReturn([[
            'offerPrices' => [['externalId' => 'ProductFDP-OFFER-1']],
        ]]);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-OFFER-1'));
            $productFdpLine  = $productFdpLines[0] ?? null;

            return $productFdpLine !== null && $productFdpLine->getQuantity() === 100;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('removes the ProductFDP (qty = 0) when FDP = 0 and a ProductFDP exists', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $productFdpProduct = makeProductFdpProduct('ProductFDP-OFFER-TO-REMOVE');
    $order       = makeCartOrder([$realProduct, $productFdpProduct], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-OFFER-TO-REMOVE'));
            $productFdpLine  = $productFdpLines[0] ?? null;

            return $productFdpLine !== null && $productFdpLine->getQuantity() === 0;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('removes the ProductFDP when the order contains only FDP products (no real products)', function () {
    $productFdpProduct = makeProductFdpProduct('ProductFDP-OFFER-ONLY');
    $order = makeCartOrder([$productFdpProduct], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $productFdpLines = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-OFFER-ONLY'));
            $productFdpLine  = $productFdpLines[0] ?? null;

            return $productFdpLine !== null && $productFdpLine->getQuantity() === 0;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('includes real product lines in the updateCartItems call', function () {
    $product1 = makeRealProduct('OFFER-A', quantity: 3);
    $product2 = makeRealProduct('OFFER-B', quantity: 5);
    $order    = makeCartOrder([$product1, $product2], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $linesA = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'OFFER-A'));
            $linesB = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'OFFER-B'));
            $offerA = $linesA[0] ?? null;
            $offerB = $linesB[0] ?? null;

            return $offerA !== null && $offerA->getQuantity() === 3
                && $offerB !== null && $offerB->getQuantity() === 5;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('ignores a real product whose variant has no offerPriceExternalId', function () {
    $variantSansOffer = new Variant();

    $productSansOffer = new Product();
    $productSansOffer->setExternalId('REAL-NO-OFFER')
        ->setQuantity(2)
        ->setVariants([$variantSansOffer]);

    $order = makeCartOrder([$productSansOffer], shippingCost: 0.0);

    $this->djustCartService->shouldReceive('updateCartItems')->never();

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('synchronises multiple orders in the same cart independently', function () {
    $product1 = makeRealProduct('OFFER-S1', quantity: 1);
    $order1   = makeCartOrder([$product1], shippingCost: 5.00, sellerExternalId: 'SUPPLIER-1');

    $product2 = makeRealProduct('OFFER-S2', quantity: 2);
    $productFdp2    = makeProductFdpProduct('ProductFDP-S2', 'SUPPLIER-2');
    $order2   = makeCartOrder([$product2, $productFdp2], shippingCost: 0.0, sellerExternalId: 'SUPPLIER-2');

    $this->djustProductService->shouldReceive('getProductOffers')
        ->once()
        ->with(ProductFdpSyncService::EXTERNAL_ID_PREFIX . 'SUPPLIER-1')
        ->andReturn([[
            'offerPrices' => [['externalId' => 'ProductFDP-S1']],
        ]]);

    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $linesS1 = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-S1'));
            $linesS2 = array_values(array_filter($lines, fn (CartItem $l) => $l->getOfferPriceId() === 'ProductFDP-S2'));
            $productFdpS1  = $linesS1[0] ?? null;
            $productFdpS2  = $linesS2[0] ?? null;

            return $productFdpS1 !== null && $productFdpS1->getQuantity() === 50
                && $productFdpS2 !== null && $productFdpS2->getQuantity() === 0;
        });

    $this->service->syncForCart('CART-001', makeCart([$order1, $order2]));
});

it('skips the FDP line and logs a warning if no offer is found (empty response)', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $order       = makeCartOrder([$realProduct], shippingCost: 20.00, sellerExternalId: 'SUPPLIER-X');

    $this->djustProductService->shouldReceive('getProductOffers')
        ->once()
        ->andReturn([]);

    $this->logger->shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'Aucun offerPriceId'));

    // Seule la ligne du produit réel est envoyée, pas de ligne FDP
    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $fdpLines = array_values(array_filter(
                $lines,
                fn (CartItem $l) => str_starts_with($l->getOfferPriceId(), ProductFdpSyncService::EXTERNAL_ID_PREFIX),
            ));

            return count($fdpLines) === 0 && count($lines) === 1;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('skips the FDP line and logs a warning if offerPrices is empty for the FDP product', function () {
    $realProduct = makeRealProduct('OFFER-001');
    $order       = makeCartOrder([$realProduct], shippingCost: 20.00, sellerExternalId: 'SUPPLIER-X');

    $this->djustProductService->shouldReceive('getProductOffers')
        ->once()
        ->andReturn([[
            'offerPrices' => [],
        ]]);

    $this->logger->shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $msg) => str_contains($msg, 'Aucun offerPriceId'));

    // Seule la ligne du produit réel est envoyée, pas de ligne FDP
    $this->djustCartService->shouldReceive('updateCartItems')
        ->once()
        ->withArgs(function (string $cartId, array $lines) {
            $fdpLines = array_values(array_filter(
                $lines,
                fn (CartItem $l) => str_starts_with($l->getOfferPriceId(), ProductFdpSyncService::EXTERNAL_ID_PREFIX),
            ));

            return count($fdpLines) === 0 && count($lines) === 1;
        });

    $this->service->syncForCart('CART-001', makeCart([$order]));
});

it('exposes the correct EXTERNAL_ID_PREFIX constant', function () {
    expect(ProductFdpSyncService::EXTERNAL_ID_PREFIX)->toBe('PRODUCT_FDP_');
});
