<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\AdherentRepository;
use App\State\Processor\AdherentPersistProcessor;
use App\State\Provider\AdherentProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['user:simple', 'adherent:get']]
        ),
        new Patch(
            openapi: new Operation(
                summary: 'Modifier un adherent',
                description: 'Permet de mettre à jour le channel, le code bonuus et les rattachements'
            ),
            security: "is_granted('ROLE_API')",
            validate: true
        ),
        new Post(),
        new GetCollection(),
    ],
    provider: AdherentProvider::class,
    processor: AdherentPersistProcessor::class
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
    #[Groups(['adherent:get', 'user:simple'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: AccordStatut::class, orphanRemoval: true)]
    private Collection $accordStatuts;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['adherent:get'])]
    private ?string $siret = null;

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

    #[Groups(['user:simple'])]
    #[ORM\Column(nullable: true)]
    private ?string $logo = null;

    #[ORM\ManyToOne(inversedBy: 'adherents')]
    private ?Channel $channel = null;

    private ?string $channelCode = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelleApe = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['user:simple'])]
    private bool $showModalStellantis = false;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['user:simple'])]
    private bool $stellantisModalValidated = false;

    #[ORM\Column]
    private ?bool $active = true;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: AdherentTarifShowcase::class, orphanRemoval: true)]
    #[Groups(['user:simple'])]
    private Collection $adherentTarifShowcases;

    private array $tarifShowcases = [];

    #[ORM\Column]
    private ?bool $hideStellantisModal = false;

    #[Groups(['user:simple'])]
    private ?bool $shouldHideStellantisModal = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:simple'])]
    private ?int $availableAccords = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:simple'])]
    private ?int $usedAccords = null;

    public function __construct()
    {
        $this->accordStatuts = new ArrayCollection();
        $this->accounts = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->adherentTarifShowcases = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): self
    {
        $this->id = $id;

        return $this;
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
        if ($this->logo !== null) {
            return $this->logo;
        }

        return $this->getParent()?->getLogo();
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getParent(): ?self
    {
        if ($this->parent === $this) {
            return null;
        }

        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getRootParent(?self $adherent = null): ?self
    {
        if (!$adherent) {
            $adherent = $this;
        }

        if (
            $adherent->getParent()?->getParent()?->getId() === $adherent->getId()
        ) {
            return null;
        }

        $parent = $adherent->getParent();

        if ($parent == null) {
            return $adherent;
        } else {
            return $this->getRootParent($parent);
        }
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

    public function getLibelleApe(): ?string
    {
        return $this->libelleApe;
    }

    public function setLibelleApe(?string $libelleApe): self
    {
        $this->libelleApe = $libelleApe;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function isShowModalStellantis(): bool
    {
        return $this->showModalStellantis;
    }

    public function setShowModalStellantis(bool $showModalStellantis): static
    {
        $this->showModalStellantis = $showModalStellantis;

        return $this;
    }

    public function isStellantisModalValidated(): bool
    {
        return $this->stellantisModalValidated;
    }

    public function setStellantisModalValidated(bool $stellantisModalValidated): self
    {
        $this->stellantisModalValidated = $stellantisModalValidated;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, AdherentTarifShowcase>
     */
    public function getAdherentTarifShowcases(): Collection
    {
        return $this->adherentTarifShowcases;
    }

    public function addAdherentTarifShowcase(AdherentTarifShowcase $adherentTarifShowcase): static
    {
        if (!$this->adherentTarifShowcases->contains($adherentTarifShowcase)) {
            $this->adherentTarifShowcases->add($adherentTarifShowcase);
            $adherentTarifShowcase->setAdherent($this);
        }

        return $this;
    }

    public function removeAdherentTarifShowcase(AdherentTarifShowcase $adherentTarifShowcase): static
    {
        if ($this->adherentTarifShowcases->removeElement($adherentTarifShowcase)) {
            // set the owning side to null (unless already changed)
            if ($adherentTarifShowcase->getAdherent() === $this) {
                $adherentTarifShowcase->setAdherent(null);
            }
        }

        return $this;
    }

    public function getTarifShowcases(): array
    {
        return $this->tarifShowcases;
    }

    public function setTarifShowcases(array $tarifShowcases): void
    {
        $this->tarifShowcases = $tarifShowcases;
    }

    public function isHideStellantisModal(): ?bool
    {
        return $this->hideStellantisModal;
    }

    public function setHideStellantisModal(?bool $hideStellantisModal): static
    {
        $this->hideStellantisModal = $hideStellantisModal;

        return $this;
    }

    public function isShouldHideStellantisModal(): ?bool
    {
        if ($this->hideStellantisModal === true) {
            return $this->hideStellantisModal;
        }

        return $this->getParent()?->isShouldHideStellantisModal();
    }

    public function setShouldHideStellantisModal(?bool $shouldHideStellantisModal): static
    {
        $this->shouldHideStellantisModal = $shouldHideStellantisModal;

        return $this;
    }

    public function getAvailableAccords(): ?int
    {
        return $this->availableAccords;
    }

    public function setAvailableAccords(int $availableAccords): static
    {
        $this->availableAccords = $availableAccords;

        return $this;
    }

    public function getUsedAccords(): ?int
    {
        return $this->usedAccords;
    }

    public function setUsedAccords(int $usedAccords): static
    {
        $this->usedAccords = $usedAccords;

        return $this;
    }
}
