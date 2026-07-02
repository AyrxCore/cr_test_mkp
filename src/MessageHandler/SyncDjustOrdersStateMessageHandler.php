<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\Djust\SyncDjustOrdersStateMessage;
use App\Service\Djust\DjustOrdersSyncService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncDjustOrdersStateMessageHandler
{
    public function __construct(
        private readonly DjustOrdersSyncService $djustOrdersSyncService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SyncDjustOrdersStateMessage $message): void
    {
        $result = $this->djustOrdersSyncService->sync();

        $this->logger->info('Djust orders sync completed.', $result);
    }
}
