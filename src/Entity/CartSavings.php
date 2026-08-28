<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartSavingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CartSavingsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CartSavings
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column(length: 255)]
    private ?string $cartId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $orderId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sellerOrderId = null;

    #[ORM\Column]
    private ?int $sellerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $partnerId = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $orderTotal = null;

    #[ORM\Column]
    private ?int $itemsTotalBeforeSavings = null;

    #[ORM\Column]
    private ?int $itemsTotal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $orderState = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
        $this->createdAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getCartId(): ?string
    {
        return $this->cartId;
    }

    public function setCartId(string $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getSellerOrderId(): ?string
    {
        return $this->sellerOrderId;
    }

    public function setSellerOrderId(?string $sellerOrderId): self
    {
        $this->sellerOrderId = $sellerOrderId;

        return $this;
    }

    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    public function setSellerId(int $sellerId): self
    {
        $this->sellerId = $sellerId;

        return $this;
    }

    public function getPartnerId(): ?string
    {
        return $this->partnerId;
    }

    public function setPartnerId(?string $partnerId): self
    {
        $this->partnerId = $partnerId;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getOrderTotal(): ?int
    {
        return $this->orderTotal;
    }

    public function setOrderTotal(int $orderTotal): static
    {
        $this->orderTotal = $orderTotal;

        return $this;
    }

    public function getItemsTotalBeforeSavings(): ?int
    {
        return $this->itemsTotalBeforeSavings;
    }

    public function setItemsTotalBeforeSavings(int $itemsTotalBeforeSavings): static
    {
        $this->itemsTotalBeforeSavings = $itemsTotalBeforeSavings;

        return $this;
    }

    public function setItemsTotal(int $itemsTotal): static
    {
        $this->itemsTotal = $itemsTotal;

        return $this;
    }

    public function getItemsTotal(): ?int
    {
        return $this->itemsTotal;
    }

    public function getOrderState(): ?string
    {
        return $this->orderState;
    }

    public function setOrderState(?string $orderState): static
    {
        $this->orderState = $orderState;

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
}
