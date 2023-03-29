<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\UserAccountInputDto;
use App\Repository\AccountRepository;
use App\State\UserAccountProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['account:get']],
        ],
    ],
    normalizationContext: ['groups' => ['account:get']]
)]
class Account
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['simpleUser', 'account:list', 'account:get'])]
    private ?Uuid $id = null;

    #[ORM\Column()]
    #[Groups(['account:list', 'account:get'])]
    private ?int $upplerUserId = null;

    #[ORM\Column()]
    #[Groups(['account:list', 'account:get'])]
    private ?int $upplerSubAccountId = null;

    #[ORM\Column]
    #[Groups(['account:list', 'account:get'])]
    private ?int $upplerCompanyId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['account:list', 'account:get'])]
    private ?string $upplerUsername = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerPassword = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('simpleUser')]
    private ?\DateTimeInterface $lastConnexion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerClientId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upplerClientSecret = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'accounts', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['account:get'])]
    private ?User $_user = null;

    #[ORM\Column]
    private ?bool $isEnabled = null;

    #[ORM\ManyToOne(inversedBy: 'accounts', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['simpleUser'])]
    private ?Adherent $adherent = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: CartSavings::class)]
    private Collection $cartSavings;

    #[ORM\Column(nullable: true)]
    #[Groups('simpleUser')]
    private ?bool $acceptCGU = false;

    public function __construct()
    {
        $this->cartSavings = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->updatedAt = new \DateTime('now');
        $this->createdAt = new \DateTime('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate()
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

    public function setUpplerUserId(int $upplerUserId): self
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

    public function setUpplerCompanyId(int $upplerCompanyId): self
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

    public function setUpplerClientId(string $upplerClientId): self
    {
        $this->upplerClientId = $upplerClientId;

        return $this;
    }

    public function getUpplerClientSecret(): ?string
    {
        return $this->upplerClientSecret;
    }

    public function setUpplerClientSecret(string $upplerClientSecret): self
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
        return $this->_user;
    }

    public function setUser(?User $_user): self
    {
        $this->_user = $_user;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

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
}
