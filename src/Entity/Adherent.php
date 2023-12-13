<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['user:simple', 'adherent:get']],
        ],
        'update' => [
            'openapi_context' => [
                'summary' => 'Modifier un adherent',
                'description' => 'Permet de mettre à jour le channel, le code bonuus et les rattachements',
            ],
            'method' => 'PATCH',
            'validate' => true,
            'denormalizationContext' => ['groups' => 'update'],
        ],
    ],
)]
#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent
{
    // DO NOT AUTO GENERATE IDs AS THEY'RE FROM NEO/SUGAR
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['update', 'adherent:get'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['adherent:get'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: AccordStatut::class, orphanRemoval: true)]
    private Collection $accordStatuts;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:simple', 'update', 'adherent:get'])]
    private ?string $reducceCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['adherent:get'])]
    private ?string $siret = null;

    #[Groups(['update'])]
    private array $attachments = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activiteApe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hashkey = null;

    #[Groups(['user:simple', 'adherent:get'])]
    #[ORM\Column(nullable: true)]
    #[Assert\Url(
        message: 'A channel logo must be an URL',
    )]
    private ?string $logo = null;

    #[ORM\ManyToOne(inversedBy: 'adherents')]
    private ?Channel $channel = null;

    #[Groups(['update'])]
    private ?string $channelCode = null;

    public function __construct()
    {
        $this->accordStatuts = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccordStatuts(): Collection
    {
        return $this->accordStatuts;
    }

    public function setAccordStatuts(Collection $accordStatuts): void
    {
        $this->accordStatuts = $accordStatuts;
    }

    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setAdherent($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            if ($account->getAdherent() === $this) {
                $account->setAdherent(null);
            }
        }

        return $this;
    }

    public function getReducceCode(): ?string
    {
        return $this->reducceCode;
    }

    public function setReducceCode(?string $reducceCode): self
    {
        $this->reducceCode = $reducceCode;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getActiviteApe(): ?string
    {
        return $this->activiteApe;
    }

    public function setActiviteApe(?string $activiteApe): self
    {
        $this->activiteApe = $activiteApe;

        return $this;
    }

    public function getHashkey(): ?string
    {
        return $this->hashkey;
    }

    public function setHashkey(?string $hashkey): self
    {
        $this->hashkey = $hashkey;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }
}
