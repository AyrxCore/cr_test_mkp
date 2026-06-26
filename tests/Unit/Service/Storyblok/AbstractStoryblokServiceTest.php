<?php

declare(strict_types=1);

use App\Service\Storyblok\AbstractStoryblokService;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(HttpClientInterface::class);
    $this->response = Mockery::mock(ResponseInterface::class);

    $this->service = new class($this->httpClient, 'test-token', 'https://api.storyblok.com/v2/cdn', 'published', new NullLogger()) extends AbstractStoryblokService {
        public function testGetStoriesRaw(array $filters = [], int $maxPages = null): array
        {
            return $this->getStoriesRaw($filters, $maxPages);
        }

        public function testRequest(string $method, string $path, array $options = []): array
        {
            return $this->request($method, $path, $options);
        }
    };
});

\afterEach(function () {
    Mockery::close();
});

\it('successfully makes a request and returns decoded response', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $this->response->shouldReceive('toArray')->once()->andReturn([
        'stories' => [
            ['slug' => 'test-1'],
            ['slug' => 'test-2'],
        ],
    ]);

    $this->httpClient->shouldReceive('request')->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', [
            'query' => [
                'token' => 'test-token',
                'version' => 'published',
            ],
        ])
        ->andReturn($this->response);

    $result = $this->service->testRequest('GET', '/stories');

    \expect($result)->toBeArray()
        ->and($result['stories'])->toHaveCount(2)
        ->and($result['stories'][0]['slug'])->toBe('test-1');
})->group('AbstractStoryblokService', 'storyblok');

\it('throws exception when API returns non-success status code', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_NOT_FOUND);
    $this->response->shouldReceive('getContent')->once()->with(false)->andReturn('Not found');

    $this->httpClient->shouldReceive('request')->once()
        ->andReturn($this->response);

    $this->service->testRequest('GET', '/stories');
})->group('AbstractStoryblokService', 'storyblok')->throws(RuntimeException::class, 'Storyblok API returned status 404');

\it('throws exception when API returns 500 status code', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_INTERNAL_SERVER_ERROR);
    $this->response->shouldReceive('getContent')->once()->with(false)->andReturn('Internal server error');

    $this->httpClient->shouldReceive('request')->once()
        ->andReturn($this->response);

    $this->service->testRequest('GET', '/stories');
})->group('AbstractStoryblokService', 'storyblok')->throws(RuntimeException::class, 'Storyblok API returned status 500');

\it('handles DecodingExceptionInterface and throws RuntimeException', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);

    // Classe anonyme pour tester DecodingExceptionInterface
    $decodingException = new class('Invalid JSON') extends Exception implements DecodingExceptionInterface {};

    $this->response->shouldReceive('toArray')->once()->andThrow($decodingException);

    $this->httpClient->shouldReceive('request')->once()
        ->andReturn($this->response);

    $this->service->testRequest('GET', '/stories');
})->group('AbstractStoryblokService', 'storyblok')->throws(RuntimeException::class, 'Failed to decode Storyblok response');

\it('handles generic Throwable and rethrows exception', function () {
    $exception = new Exception('Network error');

    $this->httpClient->shouldReceive('request')->once()
        ->andThrow($exception);

    $this->service->testRequest('GET', '/stories');
})->group('AbstractStoryblokService', 'storyblok')->throws(Exception::class, 'Network error');

\it('rethrows RuntimeException without wrapping', function () {
    $runtimeException = new RuntimeException('Already logged error');

    $this->httpClient->shouldReceive('request')->once()
        ->andThrow($runtimeException);

    $this->service->testRequest('GET', '/stories');
})->group('AbstractStoryblokService', 'storyblok')->throws(RuntimeException::class, 'Already logged error');

\it('successfully fetches stories with pagination', function () {
    $response1 = Mockery::mock(ResponseInterface::class);
    $response1->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $response1->shouldReceive('toArray')->once()->andReturn([
        'stories' => [
            ['slug' => 'news-1'],
            ['slug' => 'news-2'],
        ],
        'per_page' => 2,
        'total' => 3,
    ]);

    $this->httpClient->shouldReceive('request')->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', [
            'query' => [
                'starts_with' => 'news/',
                'page' => 1,
                'token' => 'test-token',
                'version' => 'published',
            ],
        ])
        ->andReturn($response1);

    $response2 = Mockery::mock(ResponseInterface::class);
    $response2->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $response2->shouldReceive('toArray')->once()->andReturn([
        'stories' => [
            ['slug' => 'news-3'],
        ],
        'per_page' => 2,
        'total' => 3,
    ]);

    $this->httpClient->shouldReceive('request')->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', [
            'query' => [
                'starts_with' => 'news/',
                'page' => 2,
                'token' => 'test-token',
                'version' => 'published',
            ],
        ])
        ->andReturn($response2);

    $result = $this->service->testGetStoriesRaw(['starts_with' => 'news/']);

    \expect($result['stories'])->toHaveCount(3)
        ->and($result['stories'][0]['slug'])->toBe('news-1')
        ->and($result['stories'][2]['slug'])->toBe('news-3');
})->group('AbstractStoryblokService', 'storyblok');

\it('respects maxPages parameter when fetching stories', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $this->response->shouldReceive('toArray')->once()->andReturn([
        'stories' => [
            ['slug' => 'news-1'],
            ['slug' => 'news-2'],
        ],
        'per_page' => 2,
        'total' => 10,
    ]);

    $this->httpClient->shouldReceive('request')->once()
        ->andReturn($this->response);

    $result = $this->service->testGetStoriesRaw(['starts_with' => 'news/'], 1);

    \expect($result['stories'])->toHaveCount(2)
        ->and($result['stories'][0]['slug'])->toBe('news-1');
})->group('AbstractStoryblokService', 'storyblok');

\it('handles error during getStoriesRaw and wraps in RuntimeException', function () {
    $originalException = new RuntimeException('API error');

    $this->httpClient->shouldReceive('request')->once()
        ->andThrow($originalException);

    $this->service->testGetStoriesRaw(['starts_with' => 'news/']);
})->group('AbstractStoryblokService', 'storyblok')->throws(RuntimeException::class, 'Failed to fetch stories: API error');

\it('filters out null and empty values from query params', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $this->response->shouldReceive('toArray')->once()->andReturn([
        'stories' => [],
    ]);

    $this->httpClient->shouldReceive('request')->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', [
            'query' => [
                'starts_with' => 'news/',
                'page' => 1,
                'token' => 'test-token',
                'version' => 'published',
            ],
        ])
        ->andReturn($this->response);

    $result = $this->service->testGetStoriesRaw([
        'starts_with' => 'news/',
        'empty_string' => '',
        'null_value' => null,
    ]);

    \expect($result['stories'])->toBeArray()->toBeEmpty();
})->group('AbstractStoryblokService', 'storyblok');

\it('returns empty array when no stories are available', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $this->response->shouldReceive('toArray')->once()->andReturn([
        'stories' => [],
    ]);

    $this->httpClient->shouldReceive('request')->once()->andReturn($this->response);

    $result = $this->service->testGetStoriesRaw([]);

    \expect($result['stories'])->toBeArray()->toBeEmpty();
})->group('AbstractStoryblokService', 'storyblok');
