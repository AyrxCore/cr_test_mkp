<?php

declare(strict_types=1);

use App\Service\Djust\DjustOperatorApiService;
use Psr\Log\NullLogger;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(HttpClientInterface::class);
    $this->cache = Mockery::mock(CacheInterface::class);
    $this->logger = new NullLogger();
});

\afterEach(function () {
    Mockery::close();
});

function createService(
    HttpClientInterface $httpClient,
    CacheInterface $cache,
    NullLogger $logger,
    string $baseUrl = 'https://djust-api.test.com/qantis',
    string $username = 'operator@test.com',
    string $password = 'secret',
): DjustOperatorApiService {
    return new DjustOperatorApiService($httpClient, $cache, $logger, $baseUrl, $username, $password);
}

\it('reports configured when all env vars are set', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    \expect($service->isConfigured())->toBeTrue();
})->group('DjustOperatorApiService', 'djust');

\it('reports not configured when base URL is empty', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger, baseUrl: '');

    \expect($service->isConfigured())->toBeFalse();
})->group('DjustOperatorApiService', 'djust');

\it('reports not configured when username is empty', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger, username: '');

    \expect($service->isConfigured())->toBeFalse();
})->group('DjustOperatorApiService', 'djust');

\it('reports not configured when password is empty', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger, password: '');

    \expect($service->isConfigured())->toBeFalse();
})->group('DjustOperatorApiService', 'djust');

\it('finds customer user by email from Djust API', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    $this->cache->shouldReceive('get')
        ->andReturnUsing(fn ($key, $callback) => $callback(Mockery::mock(ItemInterface::class)->shouldIgnoreMissing()));

    // Token request
    $tokenResponse = Mockery::mock(ResponseInterface::class);
    $tokenResponse->shouldReceive('getStatusCode')->andReturn(200);
    $tokenResponse->shouldReceive('getContent')->andReturn('{"token":{"accessToken":"tok123"}}');
    $tokenResponse->shouldReceive('toArray')->andReturn(['token' => ['accessToken' => 'tok123']]);

    // Customer users request
    $apiResponse = Mockery::mock(ResponseInterface::class);
    $apiResponse->shouldReceive('getStatusCode')->andReturn(200);
    $apiResponse->shouldReceive('getContent')->andReturn('{"content":[...]}');
    $apiResponse->shouldReceive('toArray')->andReturn([
        'content' => [
            [
                'customerUser' => ['id' => '0000000123', 'email' => 'user@test.com'],
                'customerAccount' => [['id' => '0000000456', 'name' => 'Test Account']],
            ],
        ],
    ]);

    $this->httpClient->shouldReceive('request')
        ->with('POST', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($tokenResponse);

    $this->httpClient->shouldReceive('request')
        ->with('GET', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($apiResponse);

    $result = $service->findCustomerUserByEmail('user@test.com');

    \expect($result)->not->toBeNull()
        ->and($result['id'])->toBe('0000000123')
        ->and($result['customerAccountId'])->toBe('0000000456');
})->group('DjustOperatorApiService', 'djust');

\it('returns null when customer user not found', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    $this->cache->shouldReceive('get')
        ->andReturnUsing(fn ($key, $callback) => $callback(Mockery::mock(ItemInterface::class)->shouldIgnoreMissing()));

    $tokenResponse = Mockery::mock(ResponseInterface::class);
    $tokenResponse->shouldReceive('getStatusCode')->andReturn(200);
    $tokenResponse->shouldReceive('getContent')->andReturn('{"token":{"accessToken":"tok123"}}');
    $tokenResponse->shouldReceive('toArray')->andReturn(['token' => ['accessToken' => 'tok123']]);

    $apiResponse = Mockery::mock(ResponseInterface::class);
    $apiResponse->shouldReceive('getStatusCode')->andReturn(200);
    $apiResponse->shouldReceive('getContent')->andReturn('{"content":[]}');
    $apiResponse->shouldReceive('toArray')->andReturn(['content' => []]);

    $this->httpClient->shouldReceive('request')
        ->with('POST', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($tokenResponse);

    $this->httpClient->shouldReceive('request')
        ->with('GET', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($apiResponse);

    $result = $service->findCustomerUserByEmail('unknown@test.com');

    \expect($result)->toBeNull();
})->group('DjustOperatorApiService', 'djust');

\it('returns null when customer user has no id', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    $this->cache->shouldReceive('get')
        ->andReturnUsing(fn ($key, $callback) => $callback(Mockery::mock(ItemInterface::class)->shouldIgnoreMissing()));

    $tokenResponse = Mockery::mock(ResponseInterface::class);
    $tokenResponse->shouldReceive('getStatusCode')->andReturn(200);
    $tokenResponse->shouldReceive('getContent')->andReturn('{"token":{"accessToken":"tok123"}}');
    $tokenResponse->shouldReceive('toArray')->andReturn(['token' => ['accessToken' => 'tok123']]);

    $apiResponse = Mockery::mock(ResponseInterface::class);
    $apiResponse->shouldReceive('getStatusCode')->andReturn(200);
    $apiResponse->shouldReceive('getContent')->andReturn('...');
    $apiResponse->shouldReceive('toArray')->andReturn([
        'content' => [
            ['customerUser' => ['email' => 'user@test.com'], 'customerAccount' => []],
        ],
    ]);

    $this->httpClient->shouldReceive('request')
        ->with('POST', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($tokenResponse);

    $this->httpClient->shouldReceive('request')
        ->with('GET', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($apiResponse);

    $result = $service->findCustomerUserByEmail('user@test.com');

    \expect($result)->toBeNull();
})->group('DjustOperatorApiService', 'djust');

\it('returns customer user with null customerAccountId when no accounts', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    $this->cache->shouldReceive('get')
        ->andReturnUsing(fn ($key, $callback) => $callback(Mockery::mock(ItemInterface::class)->shouldIgnoreMissing()));

    $tokenResponse = Mockery::mock(ResponseInterface::class);
    $tokenResponse->shouldReceive('getStatusCode')->andReturn(200);
    $tokenResponse->shouldReceive('getContent')->andReturn('{"token":{"accessToken":"tok123"}}');
    $tokenResponse->shouldReceive('toArray')->andReturn(['token' => ['accessToken' => 'tok123']]);

    $apiResponse = Mockery::mock(ResponseInterface::class);
    $apiResponse->shouldReceive('getStatusCode')->andReturn(200);
    $apiResponse->shouldReceive('getContent')->andReturn('...');
    $apiResponse->shouldReceive('toArray')->andReturn([
        'content' => [
            ['customerUser' => ['id' => '0000000123', 'email' => 'user@test.com'], 'customerAccount' => []],
        ],
    ]);

    $this->httpClient->shouldReceive('request')
        ->with('POST', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($tokenResponse);

    $this->httpClient->shouldReceive('request')
        ->with('GET', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($apiResponse);

    $result = $service->findCustomerUserByEmail('user@test.com');

    \expect($result)->not->toBeNull()
        ->and($result['id'])->toBe('0000000123')
        ->and($result['customerAccountId'])->toBeNull();
})->group('DjustOperatorApiService', 'djust');

\it('throws RuntimeException when Djust API returns error', function () {
    $service = createService($this->httpClient, $this->cache, $this->logger);

    $this->cache->shouldReceive('get')
        ->andReturnUsing(fn ($key, $callback) => $callback(Mockery::mock(ItemInterface::class)->shouldIgnoreMissing()));

    $tokenResponse = Mockery::mock(ResponseInterface::class);
    $tokenResponse->shouldReceive('getStatusCode')->andReturn(200);
    $tokenResponse->shouldReceive('getContent')->andReturn('{"token":{"accessToken":"tok123"}}');
    $tokenResponse->shouldReceive('toArray')->andReturn(['token' => ['accessToken' => 'tok123']]);

    $errorResponse = Mockery::mock(ResponseInterface::class);
    $errorResponse->shouldReceive('getStatusCode')->andReturn(500);
    $errorResponse->shouldReceive('getContent')->andReturn('Internal Server Error');

    $this->httpClient->shouldReceive('request')
        ->with('POST', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($tokenResponse);

    $this->httpClient->shouldReceive('request')
        ->with('GET', Mockery::type('string'), Mockery::type('array'))
        ->once()
        ->andReturn($errorResponse);

    $service->findCustomerUserByEmail('user@test.com');
})->throws(\RuntimeException::class)->group('DjustOperatorApiService', 'djust');

