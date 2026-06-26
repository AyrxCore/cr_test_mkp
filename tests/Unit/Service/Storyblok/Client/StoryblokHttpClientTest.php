<?php

declare(strict_types=1);

use App\Service\Storyblok\StoryblokHttpClient;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(HttpClientInterface::class);
    $this->response = Mockery::mock(ResponseInterface::class);
    $this->logger = new NullLogger();

    $this->client = new StoryblokHttpClient(
        $this->httpClient,
        'test-token',
        'https://api.storyblok.com/v2/cdn',
        'published',
        'test-token-preview',
        $this->logger
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('sends page and per_page params when fetching stories', function () {
    $this->response->shouldReceive('getStatusCode')->once()->andReturn(Response::HTTP_OK);
    $this->response->shouldReceive('getHeaders')->once()->andReturn(['total' => ['5']]);
    $this->response->shouldReceive('toArray')->once()->andReturn([
        'stories' => [
            ['slug' => 'a'],
            ['slug' => 'b'],
            ['slug' => 'c'],
            ['slug' => 'd'],
            ['slug' => 'e'],
        ],
    ]);

    $this->httpClient->shouldReceive('request')
        ->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', Mockery::on(function (array $options) {
            $query = $options['query'] ?? [];
            return isset($query['page'], $query['per_page'])
                && $query['page'] === 1
                && $query['per_page'] === 100;
        }))
        ->andReturn($this->response);

    $result = $this->client->getStories();

    \expect($result)->toHaveKey('stories')
        ->and($result['stories'])->toHaveCount(5);
})->group('StoryblokHttpClient', 'storyblok');

\it('throws RuntimeException when decoding response fails', function () {
    // Créer une exception anonyme qui implémente DecodingExceptionInterface
    $decodingException = new class('Invalid JSON') extends Exception implements DecodingExceptionInterface {};

    $this->response->shouldReceive('getStatusCode')
        ->once()
        ->andReturn(Response::HTTP_OK);

    $this->response->shouldReceive('toArray')
        ->once()
        ->andThrow($decodingException);

    $this->httpClient->shouldReceive('request')
        ->once()
        ->with('GET', 'https://api.storyblok.com/v2/cdn/stories', Mockery::any())
        ->andReturn($this->response);

    $this->client->getStories();
})->group('StoryblokHttpClient', 'storyblok')
  ->throws(RuntimeException::class, 'Failed to fetch stories: Failed to decode Storyblok response');

\it('throws exception when generic Throwable occurs', function () {
    $genericException = new Exception('Network error');

    $this->httpClient->shouldReceive('request')
        ->once()
        ->andThrow($genericException);

    $this->client->getStories();
})->group('StoryblokHttpClient', 'storyblok')
  ->throws(Exception::class, 'Network error');

\it('wraps RuntimeException from request in getStories', function () {
    $runtimeException = new RuntimeException('Request failed');

    $this->httpClient->shouldReceive('request')
        ->once()
        ->andThrow($runtimeException);

    try {
        $this->client->getStories();
        $this->fail('Expected RuntimeException was not thrown');
    } catch (RuntimeException $e) {
        // getStories() attrape la RuntimeException et la wrappe avec un nouveau message
        \expect($e->getMessage())->toContain('Failed to fetch stories');
        \expect($e->getMessage())->toContain('Request failed');
        \expect($e->getPrevious())->toBe($runtimeException);
    }
})->group('StoryblokHttpClient', 'storyblok');

\it('logs critical error when non-RuntimeException Throwable occurs', function () {
    // Utiliser un logger mockable pour vérifier le log
    $logger = Mockery::mock(Psr\Log\LoggerInterface::class);

    $client = new StoryblokHttpClient(
        $this->httpClient,
        'test-token',
        'https://api.storyblok.com/v2/cdn',
        'published',
        'test-token-preview',
        $logger
    );

    $genericException = new Exception('Unexpected error');

    $logger->shouldReceive('info')->twice(); // 2 appels info (début de getStories et request)

    $logger->shouldReceive('critical')
        ->once()
        ->with(
            'Storyblok request failed',
            Mockery::on(function ($context) {
                return isset($context['path'])
                    && isset($context['error'])
                    && isset($context['type'])
                    && $context['error'] === 'Unexpected error';
            })
        );

    $this->httpClient->shouldReceive('request')
        ->once()
        ->andThrow($genericException);

    try {
        $client->getStories();
    } catch (Exception $e) {
        // Exception attendue
    }
})->group('StoryblokHttpClient', 'storyblok');

\it('does not log critical when Throwable is RuntimeException', function () {
    // Utiliser un logger mockable pour vérifier qu'il n'y a PAS de log critical
    $logger = Mockery::mock(Psr\Log\LoggerInterface::class);

    $client = new StoryblokHttpClient(
        $this->httpClient,
        'test-token',
        'https://api.storyblok.com/v2/cdn',
        'published',
        'test-token-preview',
        $logger
    );

    $runtimeException = new RuntimeException('Runtime error');

    $logger->shouldReceive('info')->twice(); // 2 appels info (getStories et request)
    $logger->shouldReceive('error')->once(); // 1 appel error dans le catch de getStories

    // Ne devrait PAS recevoir d'appel critical car c'est déjà une RuntimeException
    $logger->shouldNotReceive('critical');

    $this->httpClient->shouldReceive('request')
        ->once()
        ->andThrow($runtimeException);

    try {
        $client->getStories();
    } catch (RuntimeException $e) {
        // Exception attendue
    }
})->group('StoryblokHttpClient', 'storyblok');

\it('wraps getStories RuntimeException with proper message', function () {
    $this->response->shouldReceive('getStatusCode')
        ->once()
        ->andReturn(Response::HTTP_INTERNAL_SERVER_ERROR);

    $this->response->shouldReceive('getContent')
        ->once()
        ->with(false)
        ->andReturn('Server error');

    $this->httpClient->shouldReceive('request')
        ->once()
        ->andReturn($this->response);

    try {
        $this->client->getStories(['starts_with' => 'news/']);
        $this->fail('Expected RuntimeException was not thrown');
    } catch (RuntimeException $e) {
        \expect($e->getMessage())->toContain('Failed to fetch stories');
        \expect($e->getMessage())->toContain('Storyblok API returned status 500');
    }
})->group('StoryblokHttpClient', 'storyblok');
