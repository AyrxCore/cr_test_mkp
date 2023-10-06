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

#[ApiResource(
    itemOperations: [
        'get',
        'update' => [
            'openapi_context' => [
                'summary' => 'Modifier un adherent',
                'description' => 'Permet de mettre a jour le code bonuus et les rattachements',
            ],
            'method' => 'PATCH',
            'validate' => true,
            'denormalizationContext' => ['groups' => 'update'],
        ],
    ]
)]
#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent
{
    // DO NOT AUTO GENERATE IDs AS THEY'RE FROM NEO/SUGAR
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['update'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: AccordStatut::class, orphanRemoval: true)]
    private Collection $accordStatuts;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['simpleUser', 'update'])]
    private ?string $reducceCode = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    /**
     * @return Collection<int, AccordStatut>
     */
    public function getAccordStatuts(): Collection
    {
        return $this->accordStatuts;
    }

    public function setAccordStatuts(Collection $accordStatuts): void
    {
        $this->accordStatuts = $accordStatuts;
    }

    /**
     * @return Collection<int, Account>
     */
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
            // set the owning side to null (unless already changed)
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
}
