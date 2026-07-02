<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustClient;
use App\Enum\Djust\DjustDefaults;
use App\Exception\UserAccountException;
use App\Service\Account\CurrentAccountProvider;
use App\Service\CredentialEncryptionService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DjustHttpClientService
{
    private const string DJUST_ACCOUNT_ACCESS_TOKEN = 'djust_account_access_token';
    private const string CACHE_KEY = 'djust_operator_token';
    private const string DJUST_ACCOUNT_REFRESH_TOKEN = 'djust_account_refresh_token';
    private const string DJUST_ACCOUNT_EXPIRES_AT = 'djust_account_expires_at';
    private const int TOKEN_EXPIRATION_TIME = 240;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        protected readonly RequestStack $requestStack,
        private readonly CredentialEncryptionService $credentialsService,
        private readonly LoggerInterface $djustLogger,
        #[Autowire(env: 'DJUST_API_BASE_URL')]
        private readonly string $baseUrl,
        #[Autowire(env: 'DJUST_API_USERNAME')]
        private readonly string $username,
        #[Autowire(env: 'DJUST_API_PASSWORD')]
        private readonly string $password,
    ) {
    }

    public function post(string $endpoint, array $data = [], array $headers = [], bool $isOperator = false): array
    {
        return $this->request('POST', $endpoint, [
            'json' => empty($data) ? new \stdClass() : $data,
            'headers' => $headers,
        ], $isOperator);
    }

    public function get(string $endpoint, array $queryParams = [], array $headers = [], bool $isOperator = false): array
    {
        return $this->request('GET', $endpoint, [
            'query' => $queryParams,
            'headers' => $headers,
        ], $isOperator);
    }

    public function getValidAccountToken(): string
    {
        $session = $this->requestStack->getSession();

        $accessToken = $session->get(self::DJUST_ACCOUNT_ACCESS_TOKEN);
        $refreshToken = $session->get(self::DJUST_ACCOUNT_REFRESH_TOKEN);
        $expiresAt = $session->get(self::DJUST_ACCOUNT_EXPIRES_AT);

        $accountData = $session->get(CurrentAccountProvider::SESSION_KEY_ACCOUNT);
        $username = $accountData->getDjustUsername();
        $password = $accountData->getDjustPassword();

        if (!$username || !$password) {
            throw new UserAccountException('[API DJUST] - Incomplete account data in session');
        }

        if ($accessToken && $expiresAt && \time() < $expiresAt) {
            return $accessToken;
        }

        try {
            $decryptedPassword = $this->credentialsService->decrypt($password);

            // Tentative de refresh token si disponible
            $newTokenData = null;
            if ($refreshToken) {
                try {
                    $newTokenData = $this->refreshAccountAccessToken($refreshToken);
                } catch (BadRequestException $e) {
                    // Le refresh token est invalide ou expiré, on le supprime
                    $session->remove(self::DJUST_ACCOUNT_REFRESH_TOKEN);
                    $this->djustLogger->info('Refresh token invalide, nouvelle authentification requise', [
                        'username' => $username,
                    ]);
                }
            }

            // Si pas de refresh token ou si le refresh a échoué, on obtient un nouveau token
            if ($newTokenData === null) {
                $newTokenData = $this->getNewAccessToken(DjustClient::ACCOUNT->value, $username, $decryptedPassword);
            }

            $session->set(self::DJUST_ACCOUNT_ACCESS_TOKEN, $newTokenData['accessToken']);
            $session->set(self::DJUST_ACCOUNT_REFRESH_TOKEN, $newTokenData['refreshToken']);
            $session->set(self::DJUST_ACCOUNT_EXPIRES_AT, $newTokenData['expireAt']);

            return $newTokenData['accessToken'];
        } catch (BadRequestException $e) {
            throw $e;
        }
    }

    public function put(string $endpoint, array $data = [], array $headers = [], bool $isOperator = false): array
    {
        return $this->request('PUT', $endpoint, [
            'json' => empty($data) ? new \stdClass() : $data,
            'headers' => $headers,
        ], $isOperator);
    }

    public function patch(string $endpoint, array $data = [], array $headers = [], bool $isOperator = false): array
    {
        return $this->request('PATCH', $endpoint, [
            'json' => empty($data) ? new \stdClass() : $data,
            'headers' => $headers,
        ], $isOperator);
    }

    public function delete(string $endpoint, array $headers = [], bool $isOperator = false): array
    {
        return $this->request('DELETE', $endpoint, [
            'headers' => $headers,
        ], $isOperator);
    }

    public function deleteWithBody(string $endpoint, array $data = [], array $headers = [], bool $isOperator = false): array
    {
        return $this->request('DELETE', $endpoint, [
            'json' => empty($data) ? new \stdClass() : $data,
            'headers' => $headers,
        ], $isOperator);
    }

    private function request(string $method, string $endpoint, array $options, bool $isOperator = false): array
    {
        $options['headers'] = $this->buildHeaders($options['headers'] ?? [], $isOperator);

        $response = $this->httpClient->request($method, $this->baseUrl.$endpoint, $options);

        $responseContent = $response->getContent();

        if ($responseContent === '') {
            return [];
        }

        return $response->toArray();
    }

    private function buildHeaders(array $headers = [], bool $isOperator = false): array
    {
        $baseHeaders = ['dj-store' => DjustDefaults::STORE->value];

        if ($isOperator) {
            return \array_merge($baseHeaders, $headers, [
                'dj-client' => DjustClient::OPERATOR->value,
                'Authorization' => 'Bearer '.$this->getValidOperatorToken(),
            ]);
        }

        $session = $this->requestStack->getSession();

        $account = $session->get(CurrentAccountProvider::SESSION_KEY_ACCOUNT);

        if (!$account) {
            throw new SessionUnavailableException('[API DJUST] - No account found in session');
        }

        $customerAccountId = $account->getDjustCustomerAccountId();
        if (!$customerAccountId) {
            throw new UserAccountException('[API DJUST] - No customer account ID found for the current account');
        }

        return \array_merge($baseHeaders, $headers, [
            'dj-client' => DjustClient::ACCOUNT->value,
            'Authorization' => 'Bearer '.$this->getValidAccountToken(),
            'customer-account-id' => $customerAccountId,
        ]);
    }

    private function refreshAccountAccessToken(string $refreshToken): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->baseUrl.DjustApiEndpoint::REFRESH_TOKEN->value, [
                'json' => [
                    'refreshToken' => $refreshToken,
                ],
                'headers' => [
                    'dj-store' => DjustDefaults::STORE->value,
                    'dj-client' => DjustClient::ACCOUNT->value,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = $response->toArray();

            return [
                'accessToken' => $data['token']['accessToken'],
                'refreshToken' => $data['token']['refreshToken'],
                'expireAt' => $data['token']['expireAt'],
            ];
        } catch (\Throwable $e) {
            throw new BadRequestException('[API DJUST] - Échec du refresh token: '.$e->getMessage());
        }
    }

    private function getNewAccessToken(string $djClient, string $username, string $password): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->baseUrl.DjustApiEndpoint::AUTH_TOKEN->value, [
                'json' => [
                    'password' => $password,
                    'username' => $username,
                ],
                'headers' => [
                    'dj-store' => DjustDefaults::STORE->value,
                    'dj-client' => $djClient,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return $response->toArray()['token'];
        } catch (\Throwable $e) {
            throw new BadRequestException('[API DJUST] - Échec de l\'obtention du token d\'accès: '.$e->getMessage());
        }
    }

    private function getValidOperatorToken(): string
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item): string {
            $tokenData = $this->getNewAccessToken(DjustClient::OPERATOR->value, $this->username, $this->password);
            $item->expiresAfter(self::TOKEN_EXPIRATION_TIME);
            $item->set($tokenData['accessToken']);

            return $tokenData['accessToken'];
        });
    }
}
