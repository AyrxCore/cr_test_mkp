<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustClient;
use App\Enum\Djust\DjustDefaults;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Djust API client for operator-level calls (no session required).
 * Uses plaintext credentials from environment (SSM in deployed envs).
 */
class DjustOperatorApiService
{
    private const string CACHE_KEY = 'djust_operator_api_token';
    private const int TOKEN_EXPIRATION_TIME = 240;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'DJUST_API_BASE_URL')]
        private readonly string $baseUrl,
        #[Autowire(env: 'DJUST_API_USERNAME')]
        private readonly string $username,
        #[Autowire(env: 'DJUST_API_PASSWORD')]
        private readonly string $password,
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->username !== '' && $this->password !== '';
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|InvalidArgumentException
     */
    public function findCustomerUserByEmail(string $email): ?array
    {
        $response = $this->get(DjustApiEndpoint::OPERATOR_CUSTOMER_USERS->value, ['search' => $email]);

        return $this->parseCustomerUserResponse($response, $email);
    }

    /**
     * Fetches customer-users matching a server-side search term (e.g. "+test").
     * The Djust API filters on the "search" query parameter and paginates the
     * result set; pagination metadata is exposed via "pagesCount".
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|InvalidArgumentException
     */
    public function fetchCustomerUsersBySearch(string $search): array
    {
        $page = 0;
        $size = 200;
        $allUsers = [];
        $totalPages = null;

        do {
            $response = $this->get(DjustApiEndpoint::OPERATOR_CUSTOMER_USERS->value, [
                'search' => $search,
                'page' => $page,
                'size' => $size,
            ]);

            if ($totalPages === null) {
                $totalPages = $response['pagesCount'] ?? $response['totalPages'] ?? 1;
            }

            $entries = $response['content'] ?? [];

            if (!\is_array($entries) || empty($entries)) {
                break;
            }

            foreach ($entries as $entry) {
                $mapped = $this->mapEntry($entry);

                if ($mapped !== null) {
                    $allUsers[] = $mapped;
                }
            }

            ++$page;
        } while ($page < $totalPages);

        return $allUsers;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOperatorToken(): string
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item): string {
            $response = $this->httpClient->request('POST', $this->baseUrl.DjustApiEndpoint::AUTH_TOKEN->value, [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                'headers' => [
                    'dj-store' => DjustDefaults::STORE->value,
                    'dj-client' => DjustClient::OPERATOR->value,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->getContent(false) !== '' ? $response->toArray(false) : [];

            if ($statusCode >= 400) {
                throw new \RuntimeException(\sprintf(
                    'Djust auth failed (HTTP %d): %s',
                    $statusCode,
                    $data['message'] ?? $data['errors'][0]['message'] ?? 'unknown error',
                ));
            }

            $token = $data['token']['accessToken'] ?? null;
            if (!$token) {
                throw new \RuntimeException('Access token missing in Djust auth response');
            }

            $item->expiresAfter(self::TOKEN_EXPIRATION_TIME);

            return $token;
        });
    }

    private function parseCustomerUserResponse(array $response, string $email): ?array
    {
        $entries = $response['content'] ?? [];

        if (!\is_array($entries) || empty($entries)) {
            return null;
        }

        foreach ($entries as $entry) {
            $mapped = $this->mapEntry($entry);

            if ($mapped === null) {
                continue;
            }

            if (\strtolower($mapped['email']) !== \strtolower($email)) {
                continue;
            }

            return [
                'id' => $mapped['id'],
                'customerAccountId' => $mapped['customerAccountId'],
            ];
        }

        return null;
    }

    private function mapEntry(array $entry): ?array
    {
        $customerUser = $entry['customerUser'] ?? null;

        if (!$customerUser || empty($customerUser['id'])) {
            return null;
        }

        $email = $customerUser['username'] ?? $customerUser['email'] ?? null;

        if ($email === null) {
            return null;
        }

        $customerAccounts = $entry['customerAccount'] ?? [];
        $customerAccountId = !empty($customerAccounts[0]['id']) ? $customerAccounts[0]['id'] : null;

        return [
            'email' => \strtolower($email),
            'id' => $customerUser['id'],
            'customerAccountId' => $customerAccountId,
        ];
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface|InvalidArgumentException
     */
    private function get(string $endpoint, array $queryParams = []): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl.$endpoint, [
            'query' => $queryParams,
            'headers' => [
                'dj-store' => DjustDefaults::STORE->value,
                'dj-client' => DjustClient::OPERATOR->value,
                'Authorization' => 'Bearer '.$this->getOperatorToken(),
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            $content = $response->getContent(false);
            $this->logger->error('[DjustOperatorApi] API error', ['status' => $statusCode, 'body' => $content]);
            throw new \RuntimeException(\sprintf('Djust API error (HTTP %d): %s', $statusCode, $content));
        }

        $content = $response->getContent(false);

        if ($content === '') {
            return [];
        }

        return $response->toArray(false);
    }
}
