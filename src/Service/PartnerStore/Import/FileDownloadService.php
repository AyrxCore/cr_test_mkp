<?php

declare(strict_types=1);

namespace App\Service\PartnerStore\Import;

use Symfony\Component\Console\Style\SymfonyStyle;

class FileDownloadService
{
    private const DOWNLOAD_TIMEOUT = 30;
    private const TEMP_FILE_PREFIX = 'partner_stores_';

    public function downloadFile(string $url, SymfonyStyle $io): array
    {
        if (!\filter_var($url, \FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("URL invalide : '{$url}'");
        }

        if (!\str_starts_with($url, 'https://')) {
            throw new \InvalidArgumentException('Seules les URLs HTTPS sont autorisées');
        }

        $io->text('Téléchargement du fichier depuis : '.$url);

        $tempFilePath = \tempnam(\sys_get_temp_dir(), self::TEMP_FILE_PREFIX).'.xlsx';

        $context = \stream_context_create([
            'http' => ['timeout' => self::DOWNLOAD_TIMEOUT],
        ]);

        $fileContent = @\file_get_contents($url, false, $context);
        if ($fileContent === false) {
            throw new \RuntimeException('Impossible de télécharger le fichier depuis : '.$url);
        }

        if (\file_put_contents($tempFilePath, $fileContent) === false) {
            throw new \RuntimeException('Impossible de sauvegarder le fichier temporaire');
        }

        $io->success(\sprintf(
            'Fichier téléchargé avec succès (%s KB)',
            \number_format(\strlen($fileContent) / 1024, 1)
        ));

        return [
            'path' => $tempFilePath,
            'isTemporary' => true,
            'size' => \strlen($fileContent),
        ];
    }

    public function cleanupTempFile(string $filePath): void
    {
        if (\file_exists($filePath)) {
            \unlink($filePath);
        }
    }
}
