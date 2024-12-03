<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Adherent;
use App\Repository\AccordStatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class AccordStatutService
{
    public function __construct(
        private AccordStatutRepository $accordStatutRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function processAccordStatutAttachments(array $attachments, Adherent $adherent): void
    {
        foreach ($attachments as $attachment) {
            $accordStatut = $this->accordStatutRepository->findOneBy([
                'adherent' => $adherent->getId(),
                'accordId' => $attachment['accordId'],
            ]);

            if ($accordStatut) {
                if (!($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_PENDING
                    && $attachment['status'] === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED)) {
                    $accordStatut->setStatus($attachment['status']);
                    $this->em->persist($accordStatut);
                }
            } else {
                $this->createAccordStatut($adherent, $attachment);
            }
        }

        $this->em->flush();
    }

    private function createAccordStatut(Adherent $adherent, array $attachment): void
    {
        $accordStatut = new AccordStatut();
        $accordStatut->setAdherent($adherent);
        $accordStatut->setAccordId(new Uuid($attachment['accordId']));
        $accordStatut->setStatus($attachment['status']);
        $this->em->persist($accordStatut);
    }
}
