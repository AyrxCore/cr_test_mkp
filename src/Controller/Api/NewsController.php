<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\News\NewsService;
use Psr\Log\LoggerInterface;

use function Sentry\captureException;

use Sentry\State\Scope;

use function Sentry\withScope;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class NewsController extends AbstractController
{
    public function __construct(
        private readonly NewsService $newsService,
        private readonly LoggerInterface $djustLogger,
    ) {
    }

    #[Route('/api/news', name: 'api_news_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $news = $this->newsService->getAll();

            return $this->json([
                'status' => 'success',
                'count' => \count($news),
                'data' => $news,
            ]);
        } catch (\Throwable $e) {
            $this->djustLogger->error('Error fetching news', [
                'exception' => $e->getMessage(),
            ]);

            withScope(function (Scope $scope) use ($e): void {
                $scope->setTag('endpoint', 'api_news_list');
                captureException($e);
            });

            return $this->json(
                ['error' => 'Failed to fetch news'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
