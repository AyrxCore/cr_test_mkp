<?php

declare(strict_types=1);

namespace App\Service\Storyblok;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StoryblokHttpClient
{
    private const array HTTP_SUCCESS_RESPONSES = [
        Response::HTTP_OK,
        Response::HTTP_ACCEPTED,
        Response::HTTP_CREATED,
        Response::HTTP_NO_CONTENT,
        Response::HTTP_PARTIAL_CONTENT,
    ];

    private const int STORIES_PER_PAGE = 100;

    public function __construct(
        private readonly HttpClientInterface $storyblokClient,
        #[Autowire('%storyblok_token%')]
        private readonly string $storyblokToken,
        #[Autowire('%storyblok_api_base_uri%')]
        private readonly string $storyblokApiBaseUri,
        #[Autowire('%env(STORYBLOK_VERSION)%')]
        private readonly string $storyblokVersion,
        #[Autowire('%env(STORYBLOK_API_TOKEN_PREVIEW)%')]
        private readonly string $storyblokPreviewToken,
        private readonly LoggerInterface $storyblokLogger,
    ) {
    }

    /**
     * Récupère toutes les stories depuis Storyblok avec pagination.
     */
    public function getStories(array $filters = [], ?int $maxPages = null, ?string $versionOverride = null): array
    {
        $queryParams = [];
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $queryParams[$key] = $value;
            }
        }

        $this->storyblokLogger->info('Fetching stories from Storyblok', [
            'filters' => $queryParams,
            'max_pages' => $maxPages,
        ]);

        try {
            $allStories = [];
            $page = 1;

            do {
                $pageQueryParams = \array_merge($queryParams, [
                    'page' => $page,
                    'per_page' => self::STORIES_PER_PAGE,
                ]);
                [$responseData, $headers] = $this->request('GET', '/stories', ['query' => $pageQueryParams], $versionOverride);

                $stories = $responseData['stories'] ?? [];
                $allStories = \array_merge($allStories, $stories);

                $total = (int) ($headers['total'][0] ?? \count($stories));
                $hasMorePages = \count($stories) === self::STORIES_PER_PAGE && \count($allStories) < $total;

                ++$page;
            } while ($hasMorePages && ($maxPages === null || $page <= $maxPages));

            $this->storyblokLogger->info('Stories fetched successfully from Storyblok', [
                'total' => \count($allStories),
                'pages_fetched' => $page - 1,
            ]);

            return ['stories' => $allStories];
        } catch (\RuntimeException $e) {
            $this->storyblokLogger->error(\sprintf('Failed to fetch stories from Storyblok: %s', $e->getMessage()), [
                'filters' => $filters,
            ]);
            throw new \RuntimeException(\sprintf('Failed to fetch stories: %s', $e->getMessage()), 0, $e);
        }
    }

    /**
     * Effectue une requête HTTP vers l'API Storyblok.
     */
    private function request(string $method, string $path, array $options = [], ?string $versionOverride = null): array
    {
        $version = $versionOverride ?? $this->storyblokVersion;

        if ($version === 'draft' && $this->storyblokPreviewToken === '') {
            throw new \RuntimeException(
                'La version "draft" est demandée mais STORYBLOK_API_TOKEN_PREVIEW n\'est pas configuré.'
            );
        }

        $token = ($version === 'draft')
            ? $this->storyblokPreviewToken
            : $this->storyblokToken;

        $options['query'] = \array_merge(
            $options['query'] ?? [],
            [
                'token' => $token,
                'version' => $version,
            ]
        );

        $fullPath = \rtrim($this->storyblokApiBaseUri, '/').$path;

        $this->storyblokLogger->info(\sprintf('Storyblok request: %s %s (version: %s)', $method, $path, $version));

        try {
            $response = $this->storyblokClient->request($method, $fullPath, $options);
            $statusCode = $response->getStatusCode();

            if (!\in_array($statusCode, self::HTTP_SUCCESS_RESPONSES, true)) {
                $errorContent = $response->getContent(false);
                $this->storyblokLogger->critical(
                    'Storyblok API error',
                    [
                        'status' => $statusCode,
                        'path' => $path,
                        'response' => $errorContent,
                    ]
                );
                throw new \RuntimeException(\sprintf('Storyblok API returned status %d', $statusCode));
            }

            $this->storyblokLogger->info(\sprintf('Storyblok success: %s', $path));

            return [$response->toArray(), $response->getHeaders()];
        } catch (DecodingExceptionInterface $e) {
            $this->storyblokLogger->critical(
                'Storyblok decoding error',
                [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]
            );
            throw new \RuntimeException('Failed to decode Storyblok response', 0, $e);
        } catch (\Throwable $e) {
            if (!$e instanceof \RuntimeException) {
                $this->storyblokLogger->critical(
                    'Storyblok request failed',
                    [
                        'path' => $path,
                        'error' => $e->getMessage(),
                        'type' => $e::class,
                    ]
                );
            }
            throw $e;
        }
    }
}
