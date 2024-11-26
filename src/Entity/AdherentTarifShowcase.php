<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\AdherentTarifShowcaseRepository;
use App\State\Processor\AdherentTarifShowcaseContactRequestProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user:simple']],
            name: 'api_adherent_tarif_showcases_get_collection',
        ),
        new Patch(
            uriTemplate: '/adherent_tarif_showcases/{id}/request-contact',
            name: 'api_adherent_tarif_showcases_request_contact',
            processor: AdherentTarifShowcaseContactRequestProcessor::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: AdherentTarifShowcaseRepository::class)]
class AdherentTarifShowcase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:simple'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adherentTarifShowcases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adherent $adherent = null;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['user:simple'])]
    private ?Uuid $accordId = null;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['user:simple'])]
    private ?Uuid $tarifId = null;

    #[ORM\Column]
    #[Groups(['user:simple'])]
    private bool $contactRequested = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): static
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function getAccordId(): ?Uuid
    {
        return $this->accordId;
    }

    public function setAccordId(Uuid $accordId): static
    {
        $this->accordId = $accordId;

        return $this;
    }

    public function getTarifId(): ?Uuid
    {
        return $this->tarifId;
    }

    public function setTarifId(Uuid $tarifId): static
    {
        $this->tarifId = $tarifId;

        return $this;
    }

    public function isContactRequested(): bool
    {
        return $this->contactRequested;
    }

    public function setContactRequested(bool $contactRequested): static
    {
        $this->contactRequested = $contactRequested;

        return $this;
    }
}
