<?php

declare(strict_types=1);

namespace App\Command;

use App\Constants\PartnerStoresExcelColumnIndices;
use App\Entity\Accord;
use App\Entity\Partner;
use App\Service\PartnerStore\Import\AccordValidationService;
use App\Service\PartnerStore\Import\ErrorHandler;
use App\Service\PartnerStore\Import\FileDownloadService;
use App\Service\PartnerStore\Import\StoreImportService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'partner-stores:import',
    description: 'Import partner stores',
)]
class ImportPartnerStoresCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileDownloadService $fileDownloadService,
        private readonly AccordValidationService $accordValidationService,
        private readonly StoreImportService $storeImportService,
        private readonly ErrorHandler $errorHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file-url', mode: InputOption::VALUE_REQUIRED, description: 'Complete S3 URL of the file to import')
            ->addOption('partner-id', mode: InputOption::VALUE_REQUIRED, description: 'Partner UUID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tempFilePath = null;
        $isTemporaryFile = false;
        $connection = null;

        try {
            $partner = $this->findAndValidatePartner($input, $io);

            $fileInfo = $this->downloadAndValidateFile($input, $io);
            $tempFilePath = $fileInfo['path'];
            $isTemporaryFile = $fileInfo['isTemporary'];

            $stores = $this->readAndValidateFileContent($tempFilePath);

            $validatedAccord = $this->validateAccords($stores, $partner, $io);

            $connection = $this->entityManager->getConnection();
            $connection->beginTransaction();

            $successCount = $this->performImport($stores, $partner, $validatedAccord, $fileInfo['path'], $io);

            // 6. Finalisation
            $this->finalizeImport($partner, $validatedAccord, $successCount, $io);

            $this->errorHandler->handleSuccess($tempFilePath, $isTemporaryFile, $connection, $io);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->errorHandler->handleError($e, $tempFilePath, $isTemporaryFile, $connection, $io);

            return Command::FAILURE;
        }
    }

    private function findAndValidatePartner(InputInterface $input, SymfonyStyle $io): Partner
    {
        $partnerId = $input->getOption('partner-id');
        $partnerRepository = $this->entityManager->getRepository(Partner::class);

        try {
            $partner = $partnerRepository->find($partnerId);

            if (!$partner) {
                throw new EntityNotFoundException("Partner avec l'ID {$partnerId} non trouvé en base de données");
            }

            $io->success("✓ Partner trouvé : {$partner->getName()} (ID: {$partnerId})");

            return $partner;
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Partner ID doit être un UUID valide : {$partnerId}");
        }
    }

    private function downloadAndValidateFile(InputInterface $input, SymfonyStyle $io): array
    {
        $fileUrl = $input->getOption('file-url');
        $io->text("URL S3 spécifiée : {$fileUrl}");

        if (\str_starts_with($fileUrl, 'https://')) {
            return $this->fileDownloadService->downloadFile($fileUrl, $io);
        }

        if (!\file_exists($fileUrl)) {
            throw new \InvalidArgumentException('Le fichier n\'existe pas : '.$fileUrl);
        }

        return [
            'path' => $fileUrl,
            'isTemporary' => false,
        ];
    }

    private function readAndValidateFileContent(string $filePath): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath, IReader::IGNORE_EMPTY_CELLS);
            $sheet = $spreadsheet->setActiveSheetIndex(0);
            $highestRow = $sheet->getHighestDataRow('A');

            $rows = $sheet->rangeToArray('A1:I'.$highestRow);
            $data = [];
            foreach ($rows as $row) {
                $data[] = $row;
            }

            $headers = \array_shift($data);
            $this->validateHeaders($headers);

            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la lecture du fichier : '.$e->getMessage());
        }
    }

    private function validateHeaders(array $headers): void
    {
        foreach (PartnerStoresExcelColumnIndices::REQUIRED_HEADERS as $index => $expectedHeader) {
            if (!isset($headers[$index]) || \trim($headers[$index]) !== $expectedHeader) {
                throw new \InvalidArgumentException(\sprintf(
                    "En-tête invalide en colonne %d. Attendu: '%s', trouvé: '%s'",
                    $index + 1,
                    $expectedHeader,
                    $headers[$index] ?? 'VIDE'
                ));
            }
        }
    }

    private function validateAccords(array $stores, Partner $partner, SymfonyStyle $io): ?Accord
    {
        $accordAnalysis = $this->accordValidationService->analyzeAccordsInFile($stores);

        if ($accordAnalysis['hasAccordIds']) {
            return $this->validateSpecificAccord($stores, $accordAnalysis, $partner, $io);
        }

        return $this->handleNoAccordIds($partner, $io);
    }

    private function validateSpecificAccord(array $stores, array $accordAnalysis, Partner $partner, SymfonyStyle $io): Accord
    {
        $io->text('Détection d\'accord IDs dans le fichier...');

        $this->accordValidationService->validateAccordConsistency($stores, $accordAnalysis['uniqueAccordIds'], $io);

        $accordId = $accordAnalysis['uniqueAccordIds'][0];

        return $this->accordValidationService->validateAccordExists($accordId, $partner, $io);
    }

    private function handleNoAccordIds(Partner $partner, SymfonyStyle $io): ?Accord
    {
        $io->text('Aucun accord ID détecté - stores liés au partner uniquement');
        $this->accordValidationService->getPartnerAccords($partner, $io);

        return null;
    }

    private function performImport(array $stores, Partner $partner, ?Accord $validatedAccord, string $filePath, SymfonyStyle $io): int
    {
        $filename = \basename(\parse_url($filePath, \PHP_URL_PATH));
        $io->title("Import des magasins pour : {$partner->getName()} (fichier: {$filename})");

        return $this->storeImportService->importStores($stores, $partner, $validatedAccord, $io);
    }

    private function finalizeImport(Partner $partner, ?Accord $validatedAccord, int $successCount, SymfonyStyle $io): void
    {
        $this->entityManager->flush();
        $io->progressFinish();

        $successMessages = [
            'Import terminé !',
            'Magasins importés avec succès: '.$successCount,
        ];

        if ($validatedAccord) {
            $successMessages[] = "Stores liés à l'accord {$validatedAccord->getId()}: {$successCount}";
        } else {
            $successMessages[] = "Stores liés au partner {$partner->getName()}: {$successCount}";
        }

        $io->success($successMessages);
    }
}
