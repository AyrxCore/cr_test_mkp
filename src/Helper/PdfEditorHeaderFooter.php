<?php

declare(strict_types=1);

namespace App\Helper;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PdfEditorHeaderFooter
{
    private string $pdfFilePath;
    private string $headerLogoPath;
    private string $footerText;

    public function __construct(string $pdfFilePath, string $headerLogoPath, string $footerText)
    {
        $this->pdfFilePath = $pdfFilePath;
        $this->headerLogoPath = $headerLogoPath;
        $this->footerText = $footerText;
    }

    /**
     * @throws PdfTypeException
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws FilterException
     * @throws \Exception
     */
    public function savePDF(): string
    {
        $pdfFilePath = $this->getPdfContent($this->pdfFilePath);
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(true, 0);
        $pageCount = $pdf->setSourceFile($pdfFilePath);
        for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
            $pdf->AddPage();
            $pdf->useTemplate($pdf->importPage($pageNo));
            $pdf->SetFont('Helvetica');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(10, 0);
            $pdf->Image($this->headerLogoPath, 10, 0, 0, 15, 'PNG');
            $pdf->getTemplateSize($pdf->importPage($pageNo));
            $pdf->SetXY(100, 200);
            $pdf->SetFontSize(6);
            $pageWidth = $pdf->GetPageWidth();
            $pdf->SetXY(0, 280);
            $pdf->Cell($pageWidth, 10, \mb_convert_encoding($this->footerText, 'ISO-8859-1', 'UTF-8'), align: 'C');
        }

        return \bin2hex($pdf->Output(name: 'S'));
    }

    /**
     * @throws \Exception
     */
    private function getPdfContent(string $path): StreamReader|string
    {
        if (!\file_exists($path)) {
            throw new FileNotFoundException();
        }
        if (\filter_var($path, \FILTER_VALIDATE_URL)) {
            if (!$pdfContent = @\file_get_contents($path)) {
                throw new \Exception('Le fichier PDF est introuvable. Veuillez contacter votre administrateur.');
            }

            return StreamReader::createByString($pdfContent);
        }

        return $path;
    }
}
