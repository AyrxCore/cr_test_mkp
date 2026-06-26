<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;

class DjustOrderService
{
    public function __construct(
        private DjustHttpClientService $djustHttpClient,
        private DjustStoreViewHeadersBuilder $storeViewHeadersBuilder,
    ) {
    }

    private function fetchOrders(array $params = []): array
    {
        $responseContent = $this->djustHttpClient->get(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            \array_merge(['locale' => DjustDefaults::LOCALE->value], $params),
            $this->storeViewHeadersBuilder->build(),
        );

        return $responseContent['content'] ?? [];
    }

    public function getOrders(array $params = []): array
    {
        $orders = $this->fetchOrders($params);

        return \array_filter($orders, fn($order) => !empty($order['orderLogistics']));
    }

    public function getOrderById(string $orderId): ?array
    {
        $formattedId = \str_pad($orderId, 10, '0', \STR_PAD_LEFT);

        $responseContent = $this->djustHttpClient->get(
            \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BY_ID->value, $formattedId),
            ['locale' => DjustDefaults::LOCALE->value],
            $this->storeViewHeadersBuilder->build(),
        );

        if (!empty($responseContent['orderLogistics'])) {
            return $responseContent;
        }

        return null;
    }

}