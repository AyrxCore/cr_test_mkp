<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups("simpleUser")]
    private ?Uuid $id = null;

    #[ORM\Column()]
    private ?int $upplerUserId = null;

    #[ORM\Column()]
    private ?int $upplerSubAccountId = null;

    #[ORM\Column]
    private ?int $upplerCompanyId = null;

    #[ORM\Column(length: 255)]
    private ?string $upplerUsername = null;

    #[ORM\Column(length: 255)]
    private ?string $upplerPassword = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("simpleUser")]
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
    private ?User $_user = null;

    #[ORM\Column]
    private ?bool $isEnabled = null;

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
}
