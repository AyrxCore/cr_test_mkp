<?php

declare(strict_types=1);

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Service\Djust\DjustAccountApiService;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

uses()->group('DjustAccountApiService', 'djust');

beforeEach(function () {
    $this->httpClient = Mockery::mock(HttpClientInterface::class);
    $this->logger = new NullLogger();
    $this->baseUrl = 'https://djust-api.test.com/qantis';
    $this->service = new DjustAccountApiService($this->httpClient, $this->logger, $this->baseUrl);
});

afterEach(function () {
    Mockery::close();
});

// --- getAccessToken ---

it('returns access token on successful authentication', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"token":{"accessToken":"tok_abc"}}');
    $response->shouldReceive('toArray')->with(false)->andReturn(['token' => ['accessToken' => 'tok_abc']]);

    $this->httpClient->shouldReceive('request')
        ->once()
        ->with('POST', $this->baseUrl.DjustApiEndpoint::AUTH_TOKEN->value, Mockery::on(
            fn ($opts) => $opts['json']['username'] === 'user@test.com'
                && $opts['json']['password'] === 'secret'
                && $opts['headers']['dj-client'] === 'ACCOUNT',
        ))
        ->andReturn($response);

    $token = $this->service->getAccessToken('user@test.com', 'secret');

    expect($token)->toBe('tok_abc');
});

it('throws RuntimeException when auth returns HTTP 401', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(401);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"message":"Unauthorized"}');
    $response->shouldReceive('toArray')->with(false)->andReturn(['message' => 'Unauthorized']);

    $this->httpClient->shouldReceive('request')->andReturn($response);

    expect(fn () => $this->service->getAccessToken('user@test.com', 'wrong'))
        ->toThrow(RuntimeException::class, 'Djust account auth failed (HTTP 401)');
});

it('throws RuntimeException when access token is missing in response', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"token":{}}');
    $response->shouldReceive('toArray')->with(false)->andReturn(['token' => []]);

    $this->httpClient->shouldReceive('request')->andReturn($response);

    expect(fn () => $this->service->getAccessToken('user@test.com', 'secret'))
        ->toThrow(RuntimeException::class, 'Access token missing');
});

// --- getOpenCarts ---

it('returns open carts content', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"content":[{"id":"0000425708","reference":"532-962-1048516","productCount":2}]}');
    $response->shouldReceive('toArray')->with(false)->andReturn([
        'content' => [
            ['id' => '0000425708', 'reference' => '532-962-1048516', 'productCount' => 2],
        ],
    ]);

    $this->httpClient->shouldReceive('request')
        ->once()
        ->with('GET', $this->baseUrl.DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value, Mockery::on(
            fn ($opts) => ($opts['query']['isValidated'] ?? null) === 'false'
                && ($opts['query']['locale'] ?? null) === DjustDefaults::LOCALE->value
                && ($opts['headers']['customer-account-id'] ?? null) === '0000092247'
                && ($opts['headers']['dj-store-view'] ?? null) === 'QANTIS_ACHAT',
        ))
        ->andReturn($response);

    $carts = $this->service->getOpenCarts('0000092247', 'tok_abc', 'QANTIS_ACHAT');

    expect($carts)->toHaveCount(1)
        ->and($carts[0]['id'])->toBe('0000425708')
        ->and($carts[0]['reference'])->toBe('532-962-1048516');
});

it('returns empty array when no open cart exists', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"content":[]}');
    $response->shouldReceive('toArray')->with(false)->andReturn(['content' => []]);

    $this->httpClient->shouldReceive('request')->andReturn($response);

    $carts = $this->service->getOpenCarts('0000092247', 'tok_abc', 'QANTIS_ACHAT');

    expect($carts)->toBeEmpty();
});

it('throws RuntimeException when getOpenCarts returns HTTP error', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(400);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"errors":[{"code":"F_E_001","message":"locale missing"}]}');
    $response->shouldReceive('toArray')->with(false)->never();

    $this->httpClient->shouldReceive('request')->andReturn($response);

    expect(fn () => $this->service->getOpenCarts('0000092247', 'tok_abc', 'QANTIS_ACHAT'))
        ->toThrow(RuntimeException::class, 'Djust API error (HTTP 400)');
});

// --- deleteCart ---

it('calls DELETE on the correct endpoint with reference', function () {
    $reference = '532-962-1048516';
    $expectedEndpoint = $this->baseUrl.sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_DELETE->value, $reference);

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getContent')->with(false)->andReturn('');

    $this->httpClient->shouldReceive('request')
        ->once()
        ->with('DELETE', $expectedEndpoint, Mockery::on(
            fn ($opts) => ($opts['headers']['customer-account-id'] ?? null) === '0000092247'
                && ($opts['headers']['dj-store-view'] ?? null) === 'QANTIS_ACHAT'
                && ($opts['headers']['dj-client'] ?? null) === 'ACCOUNT',
        ))
        ->andReturn($response);

    $this->service->deleteCart($reference, '0000092247', 'tok_abc', 'QANTIS_ACHAT');

    // No exception = success
    expect(true)->toBeTrue();
});

it('throws RuntimeException when delete returns HTTP error', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(404);
    $response->shouldReceive('getContent')->with(false)->andReturn('{"message":"Not found"}');
    $response->shouldReceive('toArray')->with(false)->never();

    $this->httpClient->shouldReceive('request')->andReturn($response);

    expect(fn () => $this->service->deleteCart('bad-ref', '0000092247', 'tok_abc', 'QANTIS_ACHAT'))
        ->toThrow(RuntimeException::class, 'Djust API error (HTTP 404)');
});
