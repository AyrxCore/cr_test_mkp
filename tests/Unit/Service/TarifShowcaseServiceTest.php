<?php

declare(strict_types=1);

use App\Entity\Adherent;
use App\Entity\AdherentTarifShowcase;
use App\Repository\AdherentTarifShowcaseRepository;
use App\Service\TarifShowcaseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->adherent = new Adherent();
    $this->adherent->setId(Uuid::v4());

    $this->tarifShowcaseRepository = Mockery::mock(AdherentTarifShowcaseRepository::class);
    $this->em = Mockery::mock(EntityManagerInterface::class);

    $this->tarifShowcaseService = new TarifShowcaseService($this->tarifShowcaseRepository, $this->em);
});

\it('creates a new TarifShowcase if none exists', function () {
    $this->tarifShowcaseRepository->shouldReceive('findBy')
        ->with(['adherent' => $this->adherent])
        ->andReturn([]);

    $tarifShowcaseData = [
        [
            'accordId' => (string)Uuid::v4(),
            'tarifId' => (string)Uuid::v4()
        ],
    ];

    $createdShowcase = null;
    $this->em->shouldReceive('persist')
        ->with(Mockery::on(function ($object) use (&$createdShowcase) {
            $createdShowcase = $object;

            return true;
        }));
    $this->em->shouldReceive('flush')->once();

    $this->tarifShowcaseService->processTarifShowcases($tarifShowcaseData, $this->adherent);

    \expect($createdShowcase)->toBeInstanceOf(AdherentTarifShowcase::class)
        ->and($createdShowcase->getAdherent())->toBe($this->adherent)
        ->and((string) $createdShowcase->getAccordId())->toBe($tarifShowcaseData[0]['accordId'])
        ->and((string) $createdShowcase->getTarifId())->toBe($tarifShowcaseData[0]['tarifId']);
})->group('tarif-showcase-service');

\it('updates an existing TarifShowcase if tarifId changes', function () {
    $accordId = Uuid::v4();
    $initialTarifId = Uuid::v4();
    $newTarifId = Uuid::v4();

    $existingShowcase = new AdherentTarifShowcase();
    $existingShowcase->setAdherent($this->adherent);
    $existingShowcase->setAccordId($accordId);
    $existingShowcase->setTarifId($initialTarifId);

    $this->tarifShowcaseRepository->shouldReceive('findBy')
        ->with(['adherent' => $this->adherent])
        ->andReturn([$existingShowcase]);

    $tarifShowcaseData = [
        [
            'accordId' => (string) $accordId,
            'tarifId' => (string) $newTarifId,
        ],
    ];

    $this->em->shouldReceive('persist')->with($existingShowcase);
    $this->em->shouldReceive('flush')->once();

    $this->tarifShowcaseService->processTarifShowcases($tarifShowcaseData, $this->adherent);

    \expect((string) $existingShowcase->getTarifId())->toBe((string) $newTarifId);

    
})->group('tarif-showcase-service');

\it('removes unused TarifShowcases', function () {
    $unusedAccordId = Uuid::v4();
    $tarifId = Uuid::v4();

    $unusedShowcase = new AdherentTarifShowcase();
    $unusedShowcase->setAdherent($this->adherent);
    $unusedShowcase->setAccordId($unusedAccordId);
    $unusedShowcase->setTarifId($tarifId);

    $this->tarifShowcaseRepository->shouldReceive('findBy')
        ->with(['adherent' => $this->adherent])
        ->andReturn([$unusedShowcase]);

    $tarifShowcaseData = [
        [
            'accordId' => (string) $unusedAccordId,
            'tarifId' => (string) $tarifId,
        ],
    ];

    $this->em->shouldReceive('remove')->with($unusedShowcase);
    $this->em->shouldReceive('flush')->once();

    $this->tarifShowcaseService->processTarifShowcases($tarifShowcaseData, $this->adherent);

    
})->group('tarif-showcase-service');
