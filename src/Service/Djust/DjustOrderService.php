<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Entity\Account;
use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Service\CredentialEncryptionService;
use Psr\Log\LoggerInterface;

class DjustOrderService
{
    public function __construct(
        private DjustHttpClientService $djustHttpClient,
        private DjustStoreViewHeadersBuilder $storeViewHeadersBuilder,
        private DjustAccountApiService $accountApiService,
        private CredentialEncryptionService $credentialEncryptionService,
        private LoggerInterface $djustLogger,
    ) {
    }

    public function getOrders(array $params = []): array
    {
        $orders = $this->fetchOrders($params);

        return \array_filter($orders, static fn ($order) => !empty($order['orderLogistics']));
    }

    public function getMostRecentBuyerOrder(): ?array
    {
        $orders = $this->fetchOrders(['sort' => 'createdAt:desc', 'size' => 1]);

        foreach ($orders as $order) {
            if (!empty($order['orderLogistics'])) {
                return $order;
            }
        }

        return null;
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

    public function getOrderByIdForAccount(string $orderId, ?Account $account): ?array
    {
        $customerAccountId = $account?->getDjustCustomerAccountId();
        $username = $account?->getDjustUsername();
        $encryptedPassword = $account?->getDjustPassword();
        $storeViewCode = $account?->getAdherent()?->getChannel()?->getCode() ?? '';

        if ($customerAccountId !== null && $username !== null && $encryptedPassword !== null && $storeViewCode !== '') {
            try {
                $plaintextPassword = $this->credentialEncryptionService->decrypt($encryptedPassword);
                $accessToken = $this->accountApiService->getAccessToken($username, $plaintextPassword);

                return $this->accountApiService->getValidatedOrderById(
                    $orderId,
                    $customerAccountId,
                    $accessToken,
                    $storeViewCode,
                );
            } catch (\Throwable $e) {
                $this->djustLogger->warning('Djust buyer order fetch failed, falling back to operator.', [
                    'orderId' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->getOrderByIdAsOperator($orderId);
    }

    public function getOrderByIdAsOperator(string $orderId): ?array
    {
        $formattedId = \str_pad($orderId, 10, '0', \STR_PAD_LEFT);

        $responseContent = $this->djustHttpClient->get(
            \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BY_ID->value, $formattedId),
            ['locale' => DjustDefaults::LOCALE->value],
            [],
            true,
        );

        if (!empty($responseContent['orderLogistics'])) {
            return $responseContent;
        }

        return null;
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
}
