<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Cgu\LegalContentService;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use function Sentry\captureException;

use Sentry\State\Scope;

use function Sentry\withScope;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class LegalContentController extends AbstractController
{
    public function __construct(
        private readonly LegalContentService $legalContentService,
        private readonly LoggerInterface $storyblokLogger,
    ) {
    }

    #[Route('/api/legal-content', name: 'api_legal_content_list', methods: ['GET'])]
    public function get(): JsonResponse
    {
        try {
            $legalContent = $this->legalContentService->getForCurrentChannel();

            if ($legalContent === null) {
                return $this->json(
                    ['error' => 'No legal content found for this channel'],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }

            return $this->json([
                'status' => 'success',
                'data' => $legalContent,
            ]);
        } catch (\Throwable $e) {
            $this->storyblokLogger->error('Error fetching legal content from Storyblok', [
                'exception' => $e->getMessage(),
            ]);

            withScope(function (Scope $scope) use ($e): void {
                $scope->setTag('endpoint', 'api_legal_content_list');
                captureException($e);
            });

            return $this->json(
                ['error' => 'Failed to fetch CGU'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
