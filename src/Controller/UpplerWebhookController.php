<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotHandledUpplerWebhookNotificationException;
use App\Service\UpplerWebhookService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UpplerWebhookController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UpplerWebhookService $upplerWebhookService,
    ) {
    }

    #[Route('/uppler-webhook', name: 'uppler_webhook', methods: ['POST'])]
    public function handleWebhookNotification(Request $request): JsonResponse
    {
        if (!$this->upplerWebhookService->isRequestAuthenticated($request)) {
            throw new UnauthorizedHttpException('UPPLER-SIGNATURE');
        }

        try {
            $this->upplerWebhookService->handleNotification($request);
        } catch (NotHandledUpplerWebhookNotificationException $e) {
            $this->logger->info('Webhook notification not handled', [
                'request' => $request->getContent(),
            ]);
        }

        return new JsonResponse();
    }
}
