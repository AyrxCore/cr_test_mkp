<?php

namespace App\Entity;

use App\Repository\UserInfoUpdateRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserInfoUpdateRequestRepository::class)]
class UserInfoUpdateRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(inversedBy: 'userInfoUpdateRequests')]
    private ?User $_user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Account $account = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:simple'])]
    private ?string $attribute = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:simple'])]
    private ?string $value = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:simple'])]
    private ?string $oldValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailChangingToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $emailChangingRequestedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['user:simple'])]
    private ?bool $isIso = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $isoAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOldValue(): ?string
    {
        return $this->oldValue;
    }

    /**
     * @param string|null $oldValue
     */
    public function setOldValue(?string $oldValue): void
    {
        $this->oldValue = $oldValue;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsIso(): ?bool
    {
        return $this->isIso;
    }

    public function setIsIso(bool $isIso): self
    {
        $this->isIso = $isIso;

        return $this;
    }

    public function getIsoAt(): ?\DateTimeImmutable
    {
        return $this->isoAt;
    }

    public function setIsoAt(?\DateTimeImmutable $isoAt): self
    {
        $this->isoAt = $isoAt;

        return $this;
    }

    public function getEmailChangingToken(): ?string
    {
        return $this->emailChangingToken;
    }

    public function setEmailChangingToken(?string $emailChangingToken): self
    {
        $this->emailChangingToken = $emailChangingToken;

        return $this;
    }

    public function getEmailChangingRequestedAt(): ?\DateTimeInterface
    {
        return $this->emailChangingRequestedAt;
    }

    public function setEmailChangingRequestedAt(?\DateTimeInterface $emailChangingRequestedAt): self
    {
        $this->emailChangingRequestedAt = $emailChangingRequestedAt;

        return $this;
    }
}
