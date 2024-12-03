<?php

declare(strict_types=1);

use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Adherent;
use App\Repository\AccordStatutRepository;
use App\Service\AccordStatutService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->adherent = new Adherent();
    $this->adherent->setId(Uuid::v4());

    $this->attachment = [
        'accordId' => Uuid::v4()->toRfc4122(),
        'status' => AccountAccordCadre::PROCESS_STATUS_ACTIVATED,
    ];

    $this->accordStatutRepository = Mockery::mock(AccordStatutRepository::class);
    $this->em = Mockery::mock(EntityManagerInterface::class);

    $this->accordStatutService = new AccordStatutService($this->accordStatutRepository, $this->em);
});

\it('processes existing AccordStatut correctly without flush', function () {
    $accordStatut = new AccordStatut();
    $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);

    $this->accordStatutRepository->shouldReceive('findOneBy')
        ->with(['adherent' => $this->adherent->getId(), 'accordId' => $this->attachment['accordId']])
        ->andReturn($accordStatut);

    $this->em->shouldReceive('persist')->with($accordStatut)->once();
    $this->em->shouldReceive('flush')->once();

    $this->accordStatutService->processAccordStatutAttachments([$this->attachment], $this->adherent);

    \expect($accordStatut->getStatus())->toBe(AccountAccordCadre::PROCESS_STATUS_ACTIVATED);
})->group('accord-statut-service')->only();

\it('creates a new AccordStatut when none exists', function () {
    // Simuler l'absence d'un AccordStatut existant
    $this->accordStatutRepository->shouldReceive('findOneBy')
        ->with(['adherent' => $this->adherent->getId(), 'accordId' => $this->attachment['accordId']])
        ->andReturnNull();

    $createdAccordStatut = null;
    $this->em->shouldReceive('persist')
        ->once()
        ->with(Mockery::on(function ($object) use (&$createdAccordStatut) {
            $createdAccordStatut = $object;

            return true;
        }));

    $this->em->shouldReceive('flush')->once();

    $this->accordStatutService->processAccordStatutAttachments([$this->attachment], $this->adherent);

    \expect($createdAccordStatut)->toBeInstanceOf(AccordStatut::class)
        ->and($createdAccordStatut->getAdherent())->toBe($this->adherent)
        ->and($createdAccordStatut->getAccordId()->toRfc4122())->toBe($this->attachment['accordId'])
        ->and($createdAccordStatut->getStatus())->toBe(AccountAccordCadre::PROCESS_STATUS_ACTIVATED);
})->group('accord-statut-service')->only();
