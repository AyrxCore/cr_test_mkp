<?php

declare(strict_types=1);

namespace App\Tests\MockClient;

use App\Enum\Djust\DjustApiEndpoint;
use App\Tests\Api\Helper\JsonHelper;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DjustMockClientCallback
{
    private static bool $simulateCartWithLogisticOrders = false;
    private static bool $simulateCartWithEcoTaxProducts = false;
    private static bool $simulateEcoTaxPatchError = false;

    public static function setSimulateCartWithLogisticOrders(bool $value): void
    {
        self::$simulateCartWithLogisticOrders = $value;
    }

    public static function setSimulateCartWithEcoTaxProducts(bool $value): void
    {
        self::$simulateCartWithEcoTaxProducts = $value;
    }

    public static function setSimulateEcoTaxPatchError(bool $value): void
    {
        self::$simulateEcoTaxPatchError = $value;
    }

    public static function reset(): void
    {
        self::$simulateCartWithLogisticOrders = false;
        self::$simulateCartWithEcoTaxProducts = false;
        self::$simulateEcoTaxPatchError = false;
    }

    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        ['path' => $path, 'query' => $query] = \parse_url($url);

        $path = \str_replace('/qantis', '', $path);

        if ($path === DjustApiEndpoint::AUTH_TOKEN->value || $path === DjustApiEndpoint::REFRESH_TOKEN->value) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/auth/gsm-response.json'));
        }

        if ($path === DjustApiEndpoint::SHOP_CUSTOMER_ACCOUNTS->value) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/accounts/gsm-response.json'));
        }

        if ($path === DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/categories/full.json'));
        }

        if ($path === DjustApiEndpoint::SHOP_SEARCH->value) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/products/search.json'));
        }

        if ($path === DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value) {
            // Si le paramètre sort est présent, c'est une requête d'orders (historique)
            if (isset($options['query']['sort'])) {
                return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/orders/orders-list.json'));
            }
            // Sinon c'est le panier actif
            if (self::$simulateCartWithEcoTaxProducts) {
                return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/carts/cart-with-eco-tax.json'));
            }
            if (self::$simulateCartWithLogisticOrders) {
                return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/carts/cart-with-logistics.json'));
            }
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/carts/cart.json'));
        }

        if (\preg_match('#^/v2/shop/logistic-orders/([^/]+)$#', $path)) {
            return new MockResponse('{}', ['http_code' => 200]);
        }

        if (\preg_match('#^/v1/shop/logistic-orders/([^/]+)/lines$#', $path)) {
            if (self::$simulateEcoTaxPatchError) {
                return new MockResponse('{"message":"Internal server error"}', ['http_code' => 500]);
            }
            return new MockResponse('{}', ['http_code' => 200]);
        }

        if (\preg_match('#^/v1/shop/commercial-orders/([^/]+)$#', $path, $matches)) {
            $orderId = $matches[1];

            // Retourne 404 si la commande n'existe pas dans les mocks
            if (!\in_array($orderId, ['0000012345'], true)) {
                return new MockResponse('{}', ['http_code' => 200]);
            }

            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/orders/order-single.json'));
        }

        if ($path === DjustApiEndpoint::SHOP_SUPPLIERS->value && $options['query']['page'] === 0) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/suppliers/suppliers-all.json'));
        }

        if (\preg_match('#^/v1/shop/products/([^/]+)$#', $path, $matches)) {
            $productId = $matches[1];

            // Retourne 404 si le produit n'existe pas dans les mocks
            if (!\in_array($productId, ['cahier_ext'], true)) {
                return new MockResponse('{"message":"Product not found"}', ['http_code' => 404]);
            }

            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/products/product.json'));
        }

        if (\preg_match('#^/v1/shop/products/([^/]+)/paginated-offers$#', $path, $matches)) {
            $productId = $matches[1];

            // Retourne 404 si les offres n'existent pas pour ce produit
            if (!\in_array($productId, ['0000001138', 'cahier_ext'], true)) {
                return new MockResponse('{"message":"Product offers not found"}', ['http_code' => 404]);
            }

            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/djust-response/products/offers.json'));
        }

        return new MockResponse();
    }
}
