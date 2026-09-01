<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\SemanticButton\SemanticButtonService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class SemanticButtonController extends AbstractController
{
    public function __construct(
        private readonly SemanticButtonService $semanticButtonService,
        private readonly LoggerInterface $storyblokLogger,
    ) {
    }

    #[Route('/api/semantic_buttons', name: 'api_semantic_buttons_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $semanticButtons = $this->semanticButtonService->getForCurrentChannel();

            return $this->json($semanticButtons, JsonResponse::HTTP_OK, [], ['skip_null_values' => true]);
        } catch (\Throwable $e) {
            $this->storyblokLogger->error('Error fetching semantic buttons from Storyblok', [
                'exception' => $e->getMessage(),
            ]);

            return $this->json(
                ['error' => 'Failed to fetch semantic buttons'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
