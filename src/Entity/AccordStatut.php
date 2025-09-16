<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AccordStatutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AccordStatutRepository::class)]
class AccordStatut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accordStatuts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adherent $adherent = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $accordId = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $accordStatutRequestAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function getAccordId(): ?Uuid
    {
        return $this->accordId;
    }

    public function setAccordId(Uuid $accordId): self
    {
        $this->accordId = $accordId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAccordStatutRequestAt(): ?\DateTimeInterface
    {
        return $this->accordStatutRequestAt;
    }

    public function setAccordStatutRequestAt(?\DateTimeInterface $accordStatutRequestAt): self
    {
        $this->accordStatutRequestAt = $accordStatutRequestAt;

        return $this;
    }
}
