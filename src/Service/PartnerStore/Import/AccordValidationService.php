<?php

declare(strict_types=1);

namespace App\Service\PartnerStore\Import;

use App\Constants\PartnerStoresExcelColumnIndices;
use App\Entity\Accord;
use App\Entity\Partner;
use App\Repository\AccordRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccordValidationService
{
    public function __construct(
        private readonly AccordRepository $accordRepository
    ) {
    }

    public function analyzeAccordsInFile(array $stores): array
    {
        $accordIds = [];
        $hasAccordIds = false;

        foreach ($stores as $storeData) {
            $accordId = isset($storeData[PartnerStoresExcelColumnIndices::COL_ACCORD_ID]) ? \trim($storeData[PartnerStoresExcelColumnIndices::COL_ACCORD_ID]) : '';
            if (!empty($accordId)) {
                $hasAccordIds = true;
                $accordIds[] = $accordId;
            }
        }

        return [
            'hasAccordIds' => $hasAccordIds,
            'accordIds' => $accordIds,
            'uniqueAccordIds' => \array_unique($accordIds),
        ];
    }

    public function validateAccordConsistency(array $stores, array $uniqueAccordIds, SymfonyStyle $io): void
    {
        if (\count($uniqueAccordIds) > 1) {
            throw new \InvalidArgumentException(
                "Fichier incohérent : plusieurs accords détectés dans le même fichier\n".
                'Accords trouvés : '.\implode(', ', $uniqueAccordIds)."\n\n".
                "Un fichier ne peut contenir qu'un seul accord. Veuillez séparer les données par accord."
            );
        }

        foreach ($stores as $storeIndex => $storeData) {
            $accordId = isset($storeData[PartnerStoresExcelColumnIndices::COL_ACCORD_ID]) ? \trim($storeData[PartnerStoresExcelColumnIndices::COL_ACCORD_ID]) : '';
            if (empty($accordId)) {
                throw new \InvalidArgumentException(
                    'Accord ID manquant à la ligne '.($storeIndex + 2)." pour le store '{$storeData[PartnerStoresExcelColumnIndices::COL_STORE_NAME]}'"
                );
            }
        }
    }

    public function validateAccordExists(string $accordId, Partner $partner, SymfonyStyle $io): Accord
    {
        $io->text("Validation de l'accord spécifié : {$accordId}");

        $accord = $this->accordRepository->find($accordId);

        if (!$accord) {
            throw new \InvalidArgumentException("Accord {$accordId} non trouvé en base de données");
        }

        if (!$accord->getPartner()->getId()->equals($partner->getId())) {
            throw new \InvalidArgumentException("L'accord {$accordId} n'appartient pas au partner {$partner->getName()}");
        }

        $io->success("✓ Accord {$accordId} validé - les stores seront liés à cet accord");

        return $accord;
    }

    public function getPartnerAccords(Partner $partner, SymfonyStyle $io): array
    {
        $partnerAccords = $this->accordRepository->findBy(['partner' => $partner]);

        if (empty($partnerAccords)) {
            $io->warning("⚠️  Aucun accord trouvé pour le partner {$partner->getName()}");
        } else {
            $accordNames = \array_map(function ($accord) {
                return $accord->getName();
            }, $partnerAccords);
            $io->text('✓ '.\count($partnerAccords).' accord(s) du partner seront marqués hasStore=true : '.\implode(', ', $accordNames));
        }

        return $partnerAccords;
    }
}
