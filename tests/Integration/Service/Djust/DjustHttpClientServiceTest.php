<?php

declare(strict_types=1);

use App\Service\Account\CurrentAccountProvider;
use App\DataFixtures\Factory\AccountFactory;
use App\Service\CredentialEncryptionService;
use App\Service\Djust\DjustHttpClientService;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(HttpClientInterface::class);
    $this->cache = $this->container->get(CacheInterface::class);
    $this->requestStack = $this->container->get(RequestStack::class);
    $this->credentialEncryptionService = $this->container->get(CredentialEncryptionService::class);
    $this->logger = new NullLogger();
    $this->session = new Session(new MockArraySessionStorage());
    $this->account = AccountFactory::createOne([
        'djustPassword' => '2MilB4yRRo9YcCBS2ntJFGVBRE1vT3R1bkJWZzNiYnEvY1U4Y1E9PQ==',
    ]);
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, $this->account);
    $request = new Request();
    $request->setSession($this->session);
    $this->requestStack->push($request);
    $this->response1 = Mockery::mock(ResponseInterface::class);
    $this->response2 = Mockery::mock(ResponseInterface::class);

    $this->baseUrl = 'https://djust-api.com';
    $this->username = 'john_doe';
    $this->password = '2MilB4yRRo9YcCBS2ntJFGVBRE1vT3R1bkJWZzNiYnEvY1U4Y1E9PQ==';
    $this->decryptedPassword = 'Pa$$word1';
    $this->djustPath = '/v1/path/djust';
    $this->idToDelete = '131244';

    $this->tokenData = [
        'token' => [
            'accessToken' => 'account_access_token',
            'refreshToken' => 'account_refresh_token',
            'expireAt' => \time() + 3600,
        ],
    ];

    $this->djustHttpClientService = new DjustHttpClientService(
        $this->httpClient,
        $this->cache,
        $this->requestStack,
        $this->credentialEncryptionService,
        $this->logger,
        $this->baseUrl,
        $this->username,
        $this->decryptedPassword
    );
});

function httpClientRequestToken($httpClient, $baseUrl, $password, $username, $djClient, $response, $tokenData)
{
    $httpClient->shouldReceive('request')->with(
        'POST',
        $baseUrl.'/auth/token',
        [
            'json' => [
                'password' => $password,
                'username' => $username,
            ],
            'headers' => [
                'dj-store' => 'default_store',
                'dj-client' => $djClient,
                'Content-Type' => 'application/json',
            ],
        ]
    )->andReturn($response);

    $response->shouldReceive('toArray')->andReturn($tokenData);
}

\it('test get method', function (string $djClient, bool $isOperator) {
    if ($isOperator) {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->decryptedPassword, $this->username, $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')->with('GET', $this->baseUrl.$this->djustPath, [
            'query' => [],
            'headers' => [
                'dj-store' => 'default_store',
                'dj-client' => $djClient,
                'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
            ],
        ])->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    } else {
        \httpClientRequestToken(
            $this->httpClient,
            $this->baseUrl,
            $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()),
            $this->account->getDjustUsername(),
            $djClient,
            $this->response1,
            $this->tokenData);

        $this->httpClient->shouldReceive('request')->with('GET', $this->baseUrl.$this->djustPath, [
            'query' => [],
            'headers' => [
                'dj-store' => 'default_store',
                'dj-client' => $djClient,
                'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
                'customer-account-id' => $this->account->getDjustCustomerAccountId(),
            ],
        ])->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    }

    $testResponse = $this->djustHttpClientService->get($this->djustPath, isOperator: $isOperator);

    if ($isOperator) {
        \expect($this->cache->getItem('djust_operator_token')->get())->toBe($this->tokenData['token']['accessToken']);
    } else {
        \expect($this->session->get('djust_account_access_token'))->toBe($this->tokenData['token']['accessToken']);
    }

    \expect($testResponse)->toBeArray();
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService');

\it('test get method with query params and headers', function (string $djClient, bool $isOperator) {
    $queryParams = ['param1' => 'value1', 'param2' => 'value2'];
    $headers = ['header1' => 'value1', 'header2' => 'value2'];
    if ($isOperator) {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->decryptedPassword, $this->username, $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('GET', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($headers, $queryParams, $djClient) {
                $expectedHeaders = \array_merge($headers, [
                    'dj-store' => 'default_store',
                    'dj-client' => $djClient,
                    'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
                ]);
                \expect(\count($options['headers']))->toBe(5);
                \expect($options['headers'])->toMatchArray($expectedHeaders);
                \expect(\count($options['query']))->toBe(2);
                \expect($options['query'])->toMatchArray($queryParams);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    } else {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()), $this->account->getDjustUsername(), $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('GET', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($headers, $queryParams, $djClient) {
                $expectedHeaders = \array_merge($headers, [
                    'dj-store' => 'default_store',
                    'dj-client' => $djClient,
                    'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
                    'customer-account-id' => $this->account->getDjustCustomerAccountId(),
                ]);
                \expect(\count($options['headers']))->toBe(6);
                \expect($options['headers'])->toMatchArray($expectedHeaders);
                \expect(\count($options['query']))->toBe(2);
                \expect($options['query'])->toMatchArray($queryParams);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    }

    $testResponse = $this->djustHttpClientService->get($this->djustPath, $queryParams, $headers, isOperator: $isOperator);

    if ($isOperator) {
        \expect($this->cache->getItem('djust_operator_token')->get())->toBe($this->tokenData['token']['accessToken']);
    } else {
        \expect($this->session->get('djust_account_access_token'))->toBe($this->tokenData['token']['accessToken']);
    }

    \expect($testResponse)->toBeArray();
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService');

\it('test post method with params', function (string $djClient, bool $isOperator) {
    $json = ['param1' => 'value1', 'param2' => 'value2'];
    if ($isOperator) {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->decryptedPassword, $this->username, $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('POST', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($json) {
                \expect(\count($options['json']))->toBe(2);
                \expect($options['json'])->toBe($json);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    } else {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()), $this->account->getDjustUsername(), $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('POST', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($json) {
                \expect(\count($options['json']))->toBe(2);
                \expect($options['json'])->toBe($json);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    }

    $testResponse = $this->djustHttpClientService->post($this->djustPath, $json, isOperator: $isOperator);

    \expect($testResponse)->toBeArray();
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService');

\it('test patch method with params', function (string $djClient, bool $isOperator) {
    $json = ['param1' => 'value1', 'param2' => 'value2'];
    if ($isOperator) {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->decryptedPassword, $this->username, $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('PATCH', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($json) {
                \expect(\count($options['json']))->toBe(2);
                \expect($options['json'])->toBe($json);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    } else {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()), $this->account->getDjustUsername(), $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('PATCH', $this->baseUrl.$this->djustPath, Mockery::on(function ($options) use ($json) {
                \expect(\count($options['json']))->toBe(2);
                \expect($options['json'])->toBe($json);

                return true;
            }))
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));

        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    }

    $testResponse = $this->djustHttpClientService->patch($this->djustPath, $json, isOperator: $isOperator);

    \expect($testResponse)->toBeArray();
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService');

\it('test delete method with id to delete in path', function (string $djClient, bool $isOperator) {
    $endpointWithId = $this->djustPath.'/'.$this->idToDelete;
    $fullUrlForMock = $this->baseUrl.$endpointWithId;

    if ($isOperator) {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->decryptedPassword, $this->username, $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('DELETE', $fullUrlForMock, [
                'headers' => [
                    'dj-store' => 'default_store',
                    'dj-client' => $djClient,
                    'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
                ],
            ])
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));
        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    } else {
        \httpClientRequestToken($this->httpClient, $this->baseUrl, $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()), $this->account->getDjustUsername(), $djClient, $this->response1, $this->tokenData);

        $this->httpClient->shouldReceive('request')
            ->with('DELETE', $fullUrlForMock, [
                'headers' => [
                    'dj-store' => 'default_store',
                    'dj-client' => $djClient,
                    'Authorization' => 'Bearer '.$this->tokenData['token']['accessToken'],
                    'customer-account-id' => $this->account->getDjustCustomerAccountId(),
                ],
            ])
            ->andReturn($this->response2);

        $this->response2->shouldReceive('getContent')->andReturn(\json_encode(['response' => 'test']));
        $this->response2->shouldReceive('toArray')->andReturn(['response' => 'test']);
    }

    $testResponse = $this->djustHttpClientService->delete($endpointWithId, isOperator: $isOperator);

    if ($isOperator) {
        \expect($this->cache->getItem('djust_operator_token')->get())->toBe($this->tokenData['token']['accessToken']);
    } else {
        \expect($this->session->get('djust_account_access_token'))->toBe($this->tokenData['token']['accessToken']);
    }

    \expect($testResponse)->toBeArray();
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService');

\it('throws BadRequestException when API request token failed', function (string $djClient, bool $isOperator) {
    $endpointWithId = $this->djustPath.'/'.$this->idToDelete;

    if ($isOperator) {
        // Vider le cache pour forcer une nouvelle requête de token
        $this->cache->clear();

        $this->httpClient->shouldReceive('request')->with(
            'POST',
            $this->baseUrl.'/auth/token',
            [
                'json' => [
                    'password' => $this->decryptedPassword,
                    'username' => $this->username,
                ],
                'headers' => [
                    'dj-store' => 'default_store',
                    'dj-client' => $djClient,
                    'Content-Type' => 'application/json',
                ],
            ]
        )->andThrow(BadRequestException::class);
    } else {
        $this->httpClient->shouldReceive('request')->with('POST', $this->baseUrl.'/auth/token', [
            'json' => [
                'password' => $this->credentialEncryptionService->decrypt($this->account->getDjustPassword()),
                'username' => $this->account->getDjustUsername(),
            ],
            'headers' => [
                'dj-store' => 'default_store',
                'dj-client' => $djClient,
                'Content-Type' => 'application/json',
            ],
        ])->andThrow(BadRequestException::class);
    }

    $this->djustHttpClientService->delete($endpointWithId, isOperator: $isOperator);
})
    ->with([
        'ACCOUNT client' => ['ACCOUNT', false],
        'OPERATOR client' => ['OPERATOR', true],
    ])
    ->group('DjustHttpClientService')->throws(BadRequestException::class);

\it('throws BadRequestException when API request fails', function () {
    $finalEndpoint = $this->djustPath.'/'.$this->idToDelete;

    $this->httpClient->shouldReceive('request')->andThrow(new BadRequestException());

    $this->djustHttpClientService->delete($finalEndpoint);
})->group('DjustHttpClientService')->throws(BadRequestException::class);

\it('throws SessionUnavailableException when session is not found', function () {
    $finalEndpoint = $this->baseUrl.$this->djustPath.'/'.$this->idToDelete;
    $this->session->clear();

    $this->djustHttpClientService->delete($finalEndpoint);
})->group('DjustHttpClientService')->throws(SessionUnavailableException::class);
