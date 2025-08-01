<?php

declare(strict_types=1);

namespace App\Service\PartnerStore\Import;

use App\Constants\PartnerStoresExcelColumnIndices;
use App\Entity\Accord;
use App\Entity\Partner;
use App\Entity\PartnerStore;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\AccordRepository;
use App\Repository\PartnerStoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StoreImportService
{
    private const FLUSH_BATCH_SIZE = 20;
    private const ADDRESS_MAX_LENGTH = 255;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PhoneFormatter $phoneFormatter,
        private readonly AccordRepository $accordRepository,
        private readonly PartnerStoreRepository $partnerStoreRepository
    ) {
    }

    public function importStores(
        array $stores,
        Partner $partner,
        ?Accord $validatedAccord,
        SymfonyStyle $io
    ): int {
        $connection = $this->entityManager->getConnection();
        if (!$connection->isTransactionActive()) {
            throw new \RuntimeException(
                'StoreImportService::importStores() DOIT être appelée dans une transaction active '
                .'pour éviter la perte de données lors de removeExistingStores()'
            );
        }

        $this->removeExistingStores($partner, $validatedAccord, $io);

        $io->progressStart(\count($stores));
        $successCount = 0;

        foreach ($stores as $storeIndex => $storeData) {
            if (empty(\trim($storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME] ?? ''))) {
                $io->warning('⚠️  Store sans nom à la ligne '.($storeIndex + 2).', ignoré');
                $io->progressAdvance();
                continue;
            }

            $partnerStore = $this->createPartnerStore($storeData, $partner, $validatedAccord, $io);

            $this->entityManager->persist($partnerStore);
            ++$successCount;

            if ($successCount % self::FLUSH_BATCH_SIZE === 0) {
                $this->flushWithErrorHandling($successCount, $io);
            }

            $io->progressAdvance();
        }

        $this->markAccordsAsHavingStores($validatedAccord, $partner, $io);

        return $successCount;
    }

    private function removeExistingStores(Partner $partner, ?Accord $validatedAccord, SymfonyStyle $io): void
    {
        if ($validatedAccord) {
            $existingStores = $validatedAccord->getStores();
            $deletedStoresCount = $existingStores->count();

            foreach ($existingStores as $store) {
                $validatedAccord->removeStore($store);
                $this->entityManager->remove($store);
            }

            if ($deletedStoresCount > 0) {
                $io->text("🗑️  Suppression de {$deletedStoresCount} ancien(s) magasin(s) de l'accord {$validatedAccord->getName()}");
            }
        } else {
            $existingStores = $this->partnerStoreRepository->findBy(['partner' => $partner]);
            $deletedStoresCount = \count($existingStores);
            $this->partnerStoreRepository->removeByPartnerId($partner->getId());

            if ($deletedStoresCount > 0) {
                $io->text("🗑️  Suppression de {$deletedStoresCount} ancien(s) magasin(s) du partner {$partner->getName()}");
            }
        }
    }

    private function createPartnerStore(array $storeData, Partner $partner, ?Accord $validatedAccord, SymfonyStyle $io): PartnerStore
    {
        $partnerStore = new PartnerStore();
        $partnerStore->setPartner($partner);
        $partnerStore->setName(\trim($storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]));
        $partnerStore->setAddress($this->formatAddress($storeData));

        $this->processPhone($storeData, $partnerStore, $io);

        $this->processCoordinates($storeData, $partnerStore, $io);

        if ($validatedAccord) {
            $validatedAccord->addStore($partnerStore);
        }

        return $partnerStore;
    }

    private function processPhone(array $storeData, PartnerStore $partnerStore, SymfonyStyle $io): void
    {
        $rawPhone = isset($storeData[PartnerStoresExcelColumnIndices::COL_PHONE]) ? \trim($storeData[PartnerStoresExcelColumnIndices::COL_PHONE]) : '';
        $formattedPhone = null;

        if (!empty($rawPhone)) {
            $formattedPhone = $this->phoneFormatter->format($rawPhone);

            if ($formattedPhone === null) {
                $io->warning("⚠️  Téléphone invalide pour le store '{$storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]}' : '{$rawPhone}'");
            }
        }

        $partnerStore->setPhone($formattedPhone);
    }

    private function processCoordinates(array $storeData, PartnerStore $partnerStore, SymfonyStyle $io): void
    {
        $lat = isset($storeData[PartnerStoresExcelColumnIndices::COL_LATITUDE]) ? \str_replace(',', '.', \trim($storeData[PartnerStoresExcelColumnIndices::COL_LATITUDE])) : '';
        $long = isset($storeData[PartnerStoresExcelColumnIndices::COL_LONGITUDE]) ? \str_replace(',', '.', \trim($storeData[PartnerStoresExcelColumnIndices::COL_LONGITUDE])) : '';

        if (empty($lat) && empty($long)) {
            $io->warning("⚠️  Coordonnées GPS manquantes pour le store '{$storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]}'");
            $partnerStore->setLatitude('');
            $partnerStore->setLongitude('');

            return;
        }

        if (empty($lat) || empty($long)) {
            $io->warning("⚠️  Coordonnées GPS incomplètes pour le store '{$storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]}' (lat: {$lat}, lng: {$long})");
            $partnerStore->setLatitude('');
            $partnerStore->setLongitude('');

            return;
        }

        if (!$this->validateCoordinatesFormat($lat, $long)) {
            $io->warning("⚠️  Coordonnées GPS invalides pour le store '{$storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]}' (lat: {$lat}, lng: {$long})");
            $partnerStore->setLatitude('');
            $partnerStore->setLongitude('');

            return;
        }

        $partnerStore->setLatitude($lat);
        $partnerStore->setLongitude($long);
    }

    private function formatAddress(array $data): string
    {
        $address = isset($data[PartnerStoresExcelColumnIndices::COL_ADDRESS]) ? \trim($data[PartnerStoresExcelColumnIndices::COL_ADDRESS]) : '';
        $complement = isset($data[PartnerStoresExcelColumnIndices::COL_ADDRESS_COMPLEMENT]) ? \trim($data[PartnerStoresExcelColumnIndices::COL_ADDRESS_COMPLEMENT]) : '';
        $postalCode = isset($data[PartnerStoresExcelColumnIndices::COL_POSTAL_CODE]) ? \trim($data[PartnerStoresExcelColumnIndices::COL_POSTAL_CODE]) : '';
        $city = isset($data[PartnerStoresExcelColumnIndices::COL_CITY]) ? \trim($data[PartnerStoresExcelColumnIndices::COL_CITY]) : '';

        $fullAddress = $address;

        if (!empty($complement)) {
            $fullAddress .= ', '.$complement;
        }

        if (!empty($postalCode)) {
            $fullAddress .= ', '.$postalCode;
        }

        if (!empty($city)) {
            $fullAddress .= ' '.$city;
        }

        return \mb_substr($fullAddress, 0, self::ADDRESS_MAX_LENGTH);
    }

    private function validateCoordinatesFormat(string $lat, string $lng): bool
    {
        return \is_numeric($lat) && \is_numeric($lng)
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180;
    }

    private function flushWithErrorHandling(int $successCount, SymfonyStyle $io): void
    {
        try {
            $this->entityManager->flush();
        } catch (\Exception $flushException) {
            $io->error("Erreur lors du flush périodique (magasin #{$successCount}): ".$flushException->getMessage());
            throw $flushException;
        }
    }

    private function markAccordsAsHavingStores(?Accord $validatedAccord, Partner $partner, SymfonyStyle $io): void
    {
        if ($validatedAccord) {
            $validatedAccord->setHasStore(true);
        } else {
            $partnerAccords = $this->accordRepository->findBy(['partner' => $partner]);
            foreach ($partnerAccords as $accord) {
                $accord->setHasStore(true);
            }
        }
    }
}
