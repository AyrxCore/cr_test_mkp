<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Context\ChannelContext;
use App\Helper\PdfEditorHeaderFooter;
use App\Utils\UnicodeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/edit-download-pdf-file')]
class PdfEditorController extends AbstractController
{
    private const AWS_WP_FILES_ROOT = 'https://ged-wp-files.s3.eu-west-3.amazonaws.com';

    /**
     * @throws \Exception
     */
    #[Route('', name: 'pdf_edit_download_file', methods: ['GET'])]
    public function downloadCustomPdfFile(Request $request, ChannelContext $channelContext): JsonResponse
    {
        if (!$relativeUrl = $request->query->get('url')) {
            return new JsonResponse(['message' => 'Url du fichier pdf manquante. Veuillez contacter votre administrateur.'], Response::HTTP_NOT_FOUND);
        }
        try {
            $channel = $channelContext->getChannel();
            $pdfFilePath = self::AWS_WP_FILES_ROOT.$relativeUrl;
            $footerText = \sprintf('Document non contractuel - Réservé exclusivement aux adhérents %s - Référence contrat : QANTIS - Ne pas diffuser', $channel->getName());
            $pdfEditor = new PdfEditorHeaderFooter($pdfFilePath, $channel->getChannelParameter()->getLogo(), $footerText);
            $filePath = \explode('/', $relativeUrl);
            $filename = \strtolower(\sprintf('%s_%s', UnicodeNormalizer::format($channel->getName()), $filePath[\count($filePath) - 1]));

            return new JsonResponse(['content' => $pdfEditor->savePDF(), 'name' => $filename]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
