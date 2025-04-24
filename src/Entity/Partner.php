<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['partner:read']]),
        new GetCollection(normalizationContext: ['groups' => ['partner:read']]),
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['upplerId' => 'exact'])]
class Partner
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['partner:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['partner:read'])]
    private ?string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['partner:read'])]
    private ?string $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: PartnerStore::class, orphanRemoval: true)]
    #[Groups(['partner:read'])]
    private Collection $partnerStores;

    #[ORM\Column(type: 'integer')]
    #[Groups(['partner:read'])]
    private ?int $upplerId = null;

    public function __construct()
    {
        $this->partnerStores = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, PartnerStore>
     */
    public function getPartnerStores(): Collection
    {
        return $this->partnerStores;
    }

    public function addPartnerStore(PartnerStore $partnerStore): static
    {
        if (!$this->partnerStores->contains($partnerStore)) {
            $this->partnerStores->add($partnerStore);
            $partnerStore->setPartner($this);
        }

        return $this;
    }

    public function removePartnerStore(PartnerStore $partnerStore): static
    {
        if ($this->partnerStores->removeElement($partnerStore)) {
            // set the owning side to null (unless already changed)
            if ($partnerStore->getPartner() === $this) {
                $partnerStore->setPartner(null);
            }
        }

        return $this;
    }

    public function getSellerId(): ?int
    {
        return $this->upplerId;
    }

    public function setUpplerId(int $upplerId): static
    {
        $this->upplerId = $upplerId;

        return $this;
    }
}
