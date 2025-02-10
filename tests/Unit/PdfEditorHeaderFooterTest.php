<?php

declare(strict_types=1);

use App\Helper\PdfEditorHeaderFooter;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

\beforeEach(function () {
    $this->pdfFilePath = 'tests/Resources/peugeot-conditions-negociees.pdf';
    $this->headerLogoPath = 'tests/Resources/logo-qantis-test.png';
    $this->footerText = 'Footer Text';
});

\it('should save a PDF with header and footer', function () {
    $pdfEditor = new PdfEditorHeaderFooter($this->pdfFilePath, $this->headerLogoPath, $this->footerText);
    $pdfContent = $pdfEditor->savePDF();

    $this->expect($pdfContent)->toBeString();
})->group('PdfEditorHeaderFooter');

\it('should handle not exist invalid PDF file path', function () {
    $pdfEditor = new PdfEditorHeaderFooter('invalid.pdf', $this->headerLogoPath, $this->footerText);

    $this->expectException(FileNotFoundException::class);
    $pdfEditor->savePDF();
})->group('PdfEditorHeaderFooter');
