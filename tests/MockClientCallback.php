<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Api\Helper\JsonHelper;
use App\Tests\MockClient\DjustMockClientCallback;
use App\Tests\MockClient\UpplerMockClientCallback;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockClientCallback
{
    private static bool $simulateStoryblokError = false;
    private static bool $simulateEmptyNews = false;
    private static bool $simulateEmptyAccordCadre = false;

    private DjustMockClientCallback $djustMock;
    private UpplerMockClientCallback $upplerMock;

    public function __construct(
        #[Autowire(env: 'DJUST_API_BASE_URL')]
        private readonly string $djustBaseUrl,
        #[Autowire(env: 'UPPLER_API_URL')]
        private readonly string $upplerBaseUrl,
    ) {
        $this->djustMock = new DjustMockClientCallback();
        $this->upplerMock = new UpplerMockClientCallback();
    }

    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        $parsedUrl = \parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? null;

        // Storyblok API requests - détecté par le path /stories
        if (\str_contains($path, '/stories') || \str_ends_with($path, '/stories')) {
            if (self::$simulateStoryblokError) {
                return new MockResponse(
                    '{"message": "Internal Server Error"}',
                    ['http_code' => 500]
                );
            }

            return $this->getStoryblokResponse($path, $query);
        }

        // Déléguer aux autres mocks selon l'URL
        if (\str_starts_with($url, $this->djustBaseUrl)) {
            return $this->djustMock->__invoke($method, $url, $options);
        }

        if (\str_starts_with($url, $this->upplerBaseUrl)) {
            return $this->upplerMock->__invoke($method, $url, $options);
        }

        // Réponse par défaut
        return new MockResponse('{}', ['http_code' => 200]);
    }

    public static function setSimulateEmptyNews(bool $simulate): void
    {
        self::$simulateEmptyNews = $simulate;
    }

    public static function setSimulateEmptyAccordCadre(bool $simulate): void
    {
        self::$simulateEmptyAccordCadre = $simulate;
    }

    public static function reset(): void
    {
        self::$simulateStoryblokError = false;
        self::$simulateEmptyNews = false;
        self::$simulateEmptyAccordCadre = false;
        DjustMockClientCallback::reset();
    }

    public static function setSimulateStoryblokError(bool $simulate): void
    {
        self::$simulateStoryblokError = $simulate;
    }

    private function getStoryblokResponse(string $path, ?string $query): MockResponse
    {
        \parse_str($query ?? '', $queryParams);

        if (self::$simulateEmptyNews || self::$simulateEmptyAccordCadre) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/storyblok-response/empty-stories.json'));
        }

        if (isset($queryParams['starts_with']) && $queryParams['starts_with'] === 'news/') {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/storyblok-response/news-list.json'));
        }

        if (isset($queryParams['starts_with']) && $queryParams['starts_with'] === 'accord-cadre/') {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/storyblok-response/accord-cadre-list.json'));
        }

        return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/storyblok-response/empty-stories.json'));
    }
}
