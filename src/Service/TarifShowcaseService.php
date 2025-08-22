<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Adherent;
use App\Entity\AdherentTarifShowcase;
use App\Repository\AdherentTarifShowcaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class TarifShowcaseService
{
    public function __construct(
        private AdherentTarifShowcaseRepository $adherentTarifShowcaseRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function processTarifShowcases(array $tarifShowcases, Adherent $adherent): void
    {
        $existingAccordIds = $this->getExistingAccordIds($adherent);
        foreach ($tarifShowcases as $tarifShowcase) {
            $accordId = new Uuid($tarifShowcase['accordId']);
            $tarifId = new Uuid($tarifShowcase['tarifId']);

            if (isset($existingAccordIds[(string) $accordId])) {
                $this->updateAndUnsetTarifShowcase($existingAccordIds, $accordId, $tarifId);
            } else {
                $this->createNewTarifShowcase($adherent, $accordId, $tarifId);
            }
        }

        $this->removeUnusedTarifShowcases($existingAccordIds);

        $this->em->flush();
    }

    private function updateAndUnsetTarifShowcase(array &$existingAccordIds, Uuid $accordId, Uuid $tarifId): void
    {
        /** @var null|AdherentTarifShowcase $existingShowcase */
        $existingShowcase = $existingAccordIds[(string) $accordId] ?? null;

        if ($existingShowcase) {
            if ((string) $existingShowcase->getTarifId() !== (string) $tarifId) {
                $existingShowcase->setTarifId($tarifId);
                $this->em->persist($existingShowcase);
            }
            unset($existingAccordIds[(string) $accordId]);
        }
    }

    private function createNewTarifShowcase(Adherent $adherent, Uuid $accordId, Uuid $tarifId): void
    {
        $newShowcase = new AdherentTarifShowcase();
        $newShowcase->setAdherent($adherent);
        $newShowcase->setAccordId($accordId);
        $newShowcase->setTarifId($tarifId);
        $this->em->persist($newShowcase);
    }

    private function removeUnusedTarifShowcases(array $existingAccordIds): void
    {
        foreach ($existingAccordIds as $unusedShowcase) {
            $this->em->remove($unusedShowcase);
        }
    }

    private function getExistingAccordIds(Adherent $adherent): array
    {
        $existingShowcases = $this->adherentTarifShowcaseRepository->findBy(['adherent' => $adherent]);
        $existingAccordIds = [];

        foreach ($existingShowcases as $existingShowcase) {
            $existingAccordIds[(string) $existingShowcase->getAccordId()] = $existingShowcase;
        }

        return $existingAccordIds;
    }
}
