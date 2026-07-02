<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustClient;
use App\Enum\Djust\DjustDefaults;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DjustAccountApiService
{

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'DJUST_API_BASE_URL')]
        private readonly string $baseUrl,
    ) {
    }

    public function getAccessToken(string $username, string $plaintextPassword): string
    {
        $response = $this->httpClient->request('POST', $this->baseUrl.DjustApiEndpoint::AUTH_TOKEN->value, [
            'json' => [
                'username' => $username,
                'password' => $plaintextPassword,
            ],
            'headers' => [
                'dj-store' => DjustDefaults::STORE->value,
                'dj-client' => DjustClient::ACCOUNT->value,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $data = $response->getContent(false) !== '' ? $response->toArray(false) : [];

        if ($statusCode >= 400) {
            throw new \RuntimeException(\sprintf(
                'Djust account auth failed (HTTP %d): %s',
                $statusCode,
                $data['message'] ?? $data['errors'][0]['message'] ?? 'unknown error',
            ));
        }

        $token = $data['token']['accessToken'] ?? null;

        if ($token === null) {
            throw new \RuntimeException('Access token missing in Djust account auth response');
        }

        return $token;
    }

    public function getOpenCarts(string $customerAccountId, string $accessToken, string $storeViewCode): array
    {
        $response = $this->request(
            'GET',
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            ['query' => ['isValidated' => 'false', 'locale' => DjustDefaults::LOCALE->value]],
            $customerAccountId,
            $accessToken,
            $storeViewCode,
        );

        return $response['content'] ?? [];
    }

    /**
     * Retrieves a validated commercial order by its numeric ID using buyer credentials.
     * Used by the cron to sync order states when operator access is unavailable.
     */
    public function getValidatedOrderById(
        string $orderId,
        string $customerAccountId,
        string $accessToken,
        string $storeViewCode,
    ): ?array {
        $formattedId = \str_pad($orderId, 10, '0', \STR_PAD_LEFT);

        try {
            $response = $this->request(
                'GET',
                DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
                ['query' => [
                    'id' => $formattedId,
                    'locale' => DjustDefaults::LOCALE->value,
                    'sort' => 'createdAt:desc',
                    'size' => 10,
                ]],
                $customerAccountId,
                $accessToken,
                $storeViewCode,
            );

            foreach ($response['content'] ?? [] as $order) {
                if (($order['id'] ?? null) === $formattedId && !empty($order['orderLogistics'])) {
                    return $order;
                }
            }

            return null;
        } catch (\Throwable $e) {
            $this->logger->warning('Djust buyer order fetch failed in getValidatedOrderById.', [
                'orderId' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function deleteCart(string $cartReference, string $customerAccountId, string $accessToken, string $storeViewCode): void
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_DELETE->value, $cartReference);
        $this->request('DELETE', $endpoint, [], $customerAccountId, $accessToken, $storeViewCode);
    }

    private function request(string $method, string $endpoint, array $options, string $customerAccountId, string $accessToken, string $storeViewCode): array
    {
        $options['headers'] = [
            'dj-store' => DjustDefaults::STORE->value,
            'dj-store-view' => $storeViewCode,
            'dj-client' => DjustClient::ACCOUNT->value,
            'Authorization' => 'Bearer '.$accessToken,
            'customer-account-id' => $customerAccountId,
        ];

        $response = $this->httpClient->request($method, $this->baseUrl.$endpoint, $options);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            $content = $response->getContent(false);
            $this->logger->error('[DjustAccountApi] API error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $statusCode,
                'body' => $content,
            ]);
            throw new \RuntimeException(\sprintf('Djust API error (HTTP %d): %s', $statusCode, $content));
        }

        $content = $response->getContent(false);

        if ($content === '') {
            return [];
        }

        return $response->toArray(false);
    }
}
