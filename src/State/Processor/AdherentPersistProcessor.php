<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use App\Service\AccordStatutService;
use App\Service\TarifShowcaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

readonly class AdherentPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private AdherentRepository $adherentRepository,
        private AccordStatutService $accordStatutService,
        private TarifShowcaseService $tarifShowcaseService,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $adherent = $this->getAdherent($data->getId());

        $this->accordStatutService->processAccordStatutAttachments($data->getAttachments(), $adherent);
        $this->tarifShowcaseService->processTarifShowcases($data->getTarifShowcases(), $adherent);

        $this->em->flush();
    }

    private function getAdherent(Uuid $adherentId): Adherent
    {
        $adherent = $this->adherentRepository->find($adherentId);
        if (!$adherent) {
            throw new NotFoundHttpException('Adherent not found');
        }

        return $adherent;
    }
}
