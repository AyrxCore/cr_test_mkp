<?php

declare(strict_types=1);

namespace App\Helper;

class PdfEditorHeaderFooter
{
    public function __construct(
        private string $pdfFilePath,
        private string $headerLogoPath,
        private string $footerText,
        private int $headerImageWidth = 25,
        private int $headerImageX = 10,
        private int $headerImageY = 0,
        private array $textStyles = ['font' => 'Helvetica', 'size' => 6, 'color' => [0, 0, 0]],
    ) {
    }

    /**
     * @throws \Exception
     */
    public function savePDF(): string
    {
        $tempFile = null;
        try {
            $pdfContent = $this->getPdfContent($this->pdfFilePath);

            $tempFile = \tempnam(\sys_get_temp_dir(), 'pdf_').'.pdf';
            \file_put_contents($tempFile, $pdfContent);

            $pdf = new \TCPDI();
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pageCount = $pdf->setSourceFile($tempFile);

            for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage();

                $pdf->useTemplate($templateId);

                $pdf->Image(
                    $this->headerLogoPath,
                    $this->headerImageX,
                    $this->headerImageY,
                    $this->headerImageWidth,
                    0,
                    'PNG'
                );

                $pdf->SetFont(
                    $this->textStyles['font'],
                    '',
                    $this->textStyles['size']
                );
                $pdf->SetTextColor(...$this->textStyles['color']);

                $textWidth = $pdf->GetStringWidth($this->footerText);
                $x = ($size['w'] - $textWidth) / 2;

                $pdf->SetXY($x, $size['h'] - 8);
                $pdf->Cell($textWidth, 10, $this->footerText, 0, 0, 'C');
            }

            $output = $pdf->Output('', 'S');

            return \bin2hex($output);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du traitement du PDF: '.$e->getMessage());
        } finally {
            if ($tempFile !== null && \file_exists($tempFile)) {
                \unlink($tempFile);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function getPdfContent(string $path): string
    {
        if (!\filter_var($path, \FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("URL du PDF invalide : {$path}");
        }

        $context = \stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);

        $pdfContent = @\file_get_contents($path, false, $context);
        if ($pdfContent === false) {
            throw new \Exception('Le fichier PDF est introuvable. Veuillez contacter votre administrateur.');
        }

        return $pdfContent;
    }
}
