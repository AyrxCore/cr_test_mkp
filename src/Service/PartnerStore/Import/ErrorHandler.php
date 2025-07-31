<?php

declare(strict_types=1);

namespace App\Service\PartnerStore\Import;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Style\SymfonyStyle;

class ErrorHandler
{
    public function __construct(
        private readonly FileDownloadService $fileDownloadService
    ) {
    }

    public function handleError(
        \Exception $exception,
        ?string $tempFilePath,
        bool $isTemporaryFile,
        ?Connection $connection,
        SymfonyStyle $io
    ): void {
        if ($connection && $connection->isTransactionActive()) {
            $connection->rollBack();
            $io->text('🔄 Transaction annulée');
        }

        if ($isTemporaryFile && $tempFilePath) {
            $this->fileDownloadService->cleanupTempFile($tempFilePath);
            $io->text('🧹 Fichier temporaire nettoyé');
        }

        $io->error('❌ '.$exception->getMessage());
    }

    public function handleSuccess(
        ?string $tempFilePath,
        bool $isTemporaryFile,
        Connection $connection,
        SymfonyStyle $io
    ): void {
        if ($connection->isTransactionActive()) {
            $connection->commit();
            $io->text('✅ Transaction validée');
        }

        if ($isTemporaryFile && $tempFilePath) {
            $this->fileDownloadService->cleanupTempFile($tempFilePath);
            $io->text('🧹 Fichier temporaire nettoyé');
        }
    }
}
