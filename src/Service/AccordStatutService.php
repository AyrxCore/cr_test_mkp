<?php

declare(strict_types=1);

namespace App\Service;

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
        $currenAccordsStatuts = $this->accordStatutRepository->findBy([
            'adherent' => $adherent->getId(),
        ]);

        // On supprime les rattachements en base qui ne sont pas dans le nouvel envoi
        // et on met à jour les status de ceux présents
        foreach ($currenAccordsStatuts as $accordStatut) {
            // On cherche si l'accord est présent dans le nouvel envoi
            $filteredAttachments = \array_filter($attachments, static fn ($attachment) => $attachment['accordId'] === (string) $accordStatut->getAccordId());
            $incomingAttachment = \array_shift($filteredAttachments);
            // On le supprime s'il n'est pas présent
            if (!$incomingAttachment) {
                $this->em->remove($accordStatut);
            } else {
                // Sinon on le met à jour
                $accordStatut->setStatus($incomingAttachment['status']);
                $this->em->persist($accordStatut);
                // On supprime les rattachements traités de l'envoi initial
                $attachments = \array_filter($attachments, static fn ($attachment) => $attachment['accordId'] !== (string) $accordStatut->getAccordId());
            }
        }

        // On créé les nouveaux rattachements restant dans l'envoi initial
        foreach ($attachments as $attachment) {
            $this->createAccordStatut($adherent, $attachment);
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
