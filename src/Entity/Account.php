<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Enum\ServiceFonction;
use App\Repository\AccountRepository;
use App\State\Provider\AccountProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['account:get']],
            security: 'is_granted("ROLE_API") or object.getUser() == user'
        ),
        new Get(
            openapi: new Operation(
                summary: 'Select an Account',
                description: 'Select an Account to use when communicating with Uppler'
            ),
            name: 'account_select',
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['user:simple', 'account:get', 'account:external_api_data:buyer']],
            name: 'api_accounts_get_collection',
            provider: AccountProvider::class
        )]
)]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['user:simple', 'account:list', 'account:get'])]
    private ?Uuid $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:simple', 'account:list', 'account:get'])]
    private ?int $upplerUserId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['account:list', 'account:get'])]
    private ?int $upplerSubAccountId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['account:list', 'account:get'])]
    private ?int $upplerCompanyId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['account:list', 'account:get'])]
    private ?string $upplerUsername = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerPassword = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('user:simple')]
    private ?\DateTimeInterface $lastConnexion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerClientId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerClientSecret = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['account:get'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['account:get', 'user:simple'])]
    private ?bool $enabled = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:simple', 'account:get'])]
    private ?Adherent $adherent = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: CartSavings::class)]
    private Collection $cartSavings;

    #[ORM\Column(nullable: true)]
    #[Groups('user:simple')]
    private ?bool $acceptCGU = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('user:simple')]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Favorite::class)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: SavedCart::class, orphanRemoval: true)]
    private Collection $savedCarts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serviceFonction = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $contactId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $djustCustomerAccountId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $djustCustomerUserId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $djustUsername = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $djustPassword = null;

    private string $serviceFonctionLabel = '';

    public function __construct()
    {
        $this->cartSavings = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->savedCarts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->upplerClientId;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTime('now');
        $this->createdAt = new \DateTime('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime('now');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUpplerUserId(): ?int
    {
        return $this->upplerUserId;
    }

    public function setUpplerUserId(?int $upplerUserId): self
    {
        $this->upplerUserId = $upplerUserId;

        return $this;
    }

    public function getUpplerSubAccountId(): ?int
    {
        return $this->upplerSubAccountId;
    }

    public function setUpplerSubAccountId(?int $upplerSubAccountId): self
    {
        $this->upplerSubAccountId = $upplerSubAccountId;

        return $this;
    }

    public function getUpplerCompanyId(): ?int
    {
        return $this->upplerCompanyId;
    }

    public function setUpplerCompanyId(?int $upplerCompanyId): self
    {
        $this->upplerCompanyId = $upplerCompanyId;

        return $this;
    }

    public function getUpplerUsername(): ?string
    {
        return $this->upplerUsername;
    }

    public function setUpplerUsername(string $upplerUsername): self
    {
        $this->upplerUsername = $upplerUsername;

        return $this;
    }

    public function getUpplerPassword(): ?string
    {
        return $this->upplerPassword;
    }

    public function setUpplerPassword(string $upplerPassword): self
    {
        $this->upplerPassword = $upplerPassword;

        return $this;
    }

    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTimeInterface $lastConnexion): self
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }

    public function getUpplerClientId(): ?string
    {
        return $this->upplerClientId;
    }

    public function setUpplerClientId(?string $upplerClientId): self
    {
        $this->upplerClientId = $upplerClientId;

        return $this;
    }

    public function getUpplerClientSecret(): ?string
    {
        return $this->upplerClientSecret;
    }

    public function setUpplerClientSecret(?string $upplerClientSecret): self
    {
        $this->upplerClientSecret = $upplerClientSecret;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
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

    /**
     * @return Collection<int, CartSavings>
     */
    public function getCartSavings(): Collection
    {
        return $this->cartSavings;
    }

    public function addCartSaving(CartSavings $cartSaving): self
    {
        if (!$this->cartSavings->contains($cartSaving)) {
            $this->cartSavings->add($cartSaving);
            $cartSaving->setAccount($this);
        }

        return $this;
    }

    public function removeCartSaving(CartSavings $cartSaving): self
    {
        if ($this->cartSavings->removeElement($cartSaving)) {
            // set the owning side to null (unless already changed)
            if ($cartSaving->getAccount() === $this) {
                $cartSaving->setAccount(null);
            }
        }

        return $this;
    }

    public function isAcceptCGU(): ?bool
    {
        return $this->acceptCGU;
    }

    public function setAcceptCGU(?bool $acceptCGU): self
    {
        $this->acceptCGU = $acceptCGU;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setAccount($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getAccount() === $this) {
                $favorite->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SavedCart>
     */
    public function getSavedCarts(): Collection
    {
        return $this->savedCarts;
    }

    public function addSavedCart(SavedCart $savedCart): self
    {
        if (!$this->savedCarts->contains($savedCart)) {
            $this->savedCarts->add($savedCart);
            $savedCart->setAccount($this);
        }

        return $this;
    }

    public function removeSavedCart(SavedCart $savedCart): self
    {
        if ($this->savedCarts->removeElement($savedCart)) {
            // set the owning side to null (unless already changed)
            if ($savedCart->getAccount() === $this) {
                $savedCart->setAccount(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getServiceFonction(): ?string
    {
        return $this->serviceFonction;
    }

    public function setServiceFonction(?string $serviceFonction): self
    {
        $this->serviceFonction = $serviceFonction;

        return $this;
    }

    public function getContactId(): ?Uuid
    {
        return $this->contactId;
    }

    public function setContactId(?Uuid $contactId): self
    {
        $this->contactId = $contactId;

        return $this;
    }

    public function getDjustCustomerAccountId(): ?string
    {
        return $this->djustCustomerAccountId;
    }

    public function setDjustCustomerAccountId(?string $djustCustomerAccountId): static
    {
        $this->djustCustomerAccountId = $djustCustomerAccountId;

        return $this;
    }

    public function getDjustCustomerUserId(): ?string
    {
        return $this->djustCustomerUserId;
    }

    public function setDjustCustomerUserId(?string $djustCustomerUserId): static
    {
        $this->djustCustomerUserId = $djustCustomerUserId;

        return $this;
    }

    public function getDjustUsername(): ?string
    {
        return $this->djustUsername;
    }

    public function setDjustUsername(?string $djustUsername): static
    {
        $this->djustUsername = $djustUsername;

        return $this;
    }

    public function getDjustPassword(): ?string
    {
        return $this->djustPassword;
    }

    public function setDjustPassword(?string $djustPassword): static
    {
        $this->djustPassword = $djustPassword;

        return $this;
    }

    public function getServiceFonctionLabel(): ?string
    {
        return $this->serviceFonction ? ServiceFonction::format($this->serviceFonction) : null;
    }
}
