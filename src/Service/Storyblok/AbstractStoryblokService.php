<?php

declare(strict_types=1);

namespace App\Service\Storyblok;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractStoryblokService
{
    private const HTTP_SUCCESS_RESPONSES = [
        Response::HTTP_OK,
        Response::HTTP_ACCEPTED,
        Response::HTTP_CREATED,
        Response::HTTP_NO_CONTENT,
        Response::HTTP_PARTIAL_CONTENT,
    ];

    public function __construct(
        protected HttpClientInterface $storyblokClient,
        #[Autowire('%storyblok_token%')]
        protected string $storyblokToken,
        #[Autowire('%storyblok_api_base_uri%')]
        protected string $storyblokApiBaseUri,
        #[Autowire('%env(STORYBLOK_VERSION)%')]
        protected string $storyblokVersion,
        protected readonly LoggerInterface $storyblokLogger,
    ) {
    }

    protected function getStoriesRaw(array $filters = [], ?int $maxPages = null): array
    {
        $queryParams = [];
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $queryParams[$key] = $value;
            }
        }

        $maxPages = $maxPages ?? $filters['max_pages'] ?? null;
        unset($queryParams['max_pages']);

        $this->storyblokLogger->info('Fetching stories with filters', [
            'filters' => $queryParams,
            'version' => $this->storyblokVersion,
            'max_pages' => $maxPages,
        ]);

        try {
            $allStories = [];
            $page = 1;
            $totalStories = 0;

            do {
                $pageQueryParams = \array_merge($queryParams, ['page' => $page]);
                $responseData = $this->request(
                    'GET',
                    '/stories',
                    ['query' => $pageQueryParams]
                );

                $stories = $responseData['stories'] ?? [];
                $allStories = \array_merge($allStories, $stories);
                $totalStories += \count($stories);

                $total = $responseData['total'] ?? $totalStories;
                $hasMorePages = \count($stories) > 0 && $totalStories < $total;

                ++$page;
            } while ($hasMorePages && ($maxPages === null || $page <= $maxPages));

            $this->storyblokLogger->info('Stories fetched successfully', [
                'total' => $totalStories,
                'pages_fetched' => $page - 1,
            ]);

            return [
                'stories' => $allStories,
            ];
        } catch (\RuntimeException $e) {
            $this->storyblokLogger->error(\sprintf('Failed to fetch stories: %s', $e->getMessage()), [
                'filters' => $filters,
            ]);
            throw new \RuntimeException(\sprintf('Failed to fetch stories: %s', $e->getMessage()), 0, $e);
        }
    }

    protected function request(
        string $method,
        string $path,
        array $options = [],
    ): array {
        $options['query'] = \array_merge(
            $options['query'] ?? [],
            [
                'token' => $this->storyblokToken,
                'version' => $this->storyblokVersion,
            ]
        );

        $fullPath = \rtrim($this->storyblokApiBaseUri, '/').$path;

        $this->storyblokLogger->info(\sprintf('Storyblok request: %s %s (version: %s)', $method, $path, $this->storyblokVersion));

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

            return $response->toArray();
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
