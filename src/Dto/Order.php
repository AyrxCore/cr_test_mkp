<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;

#[ApiResource(
    collectionOperations: [
        'get' => [
            "openapi_context" => [
                'summary' => "Liste des commandes",
                'description' => "Cette opération permet de récupérer la liste des commandes associées à un acheteur."
            ],
            "validate" => true,
        ],
    ],
    itemOperations: [
        'get' => [
            "openapi_context" => [
                'summary' => "Récupérer un historique par son identifiant",
                'description' => "Cette opération permet de récupérer les informations d'une commande en spécifiant son identifiant unique."
            ],
            'requirements' => ['id' => '\d+'],
        ],
    ]
)]
final class Order
{
    public const ORDER_NEW = 'NEW';
    public const ORDER_PENDING = 'PENDING';
    public const ORDER_CONFIRMED = 'CONFIRMED';
    public const ORDER_EDITED = 'EDITED';
    public const ORDER_REFUSED = 'REFUSED';
    public const ORDER_EXPIRED = 'EXPIRED';
    public const ORDER_CANCELED = 'CANCELED';

    #[ApiProperty(identifier: true)]
    private ?int $id = null;
    private string $orderNumber;
    private array $items = [];
    private float $shipmentAmount = 0;
    private float $total = 0;
    private float $totalExcludingTaxes = 0;
    private ?string $state = null;
    private ?string $billingAddress = null;
    private ?string $shippingAddress = null;
    private ?string $shippingState = null;
    private ?int $paymentId = null;
    private ?DateTimeInterface $createdAt = null;
    private ?DateTimeInterface $updatedAt = null;
    private ?DateTimeInterface $confirmedAt = null;
    private ?DateTimeInterface $shippedAt = null;
    private ?DateTimeInterface $deliveredAt = null;
    private ?DateTimeInterface $canceledAt = null;
    private ?DateTimeInterface $refusedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getTotalExcludingTaxes(): float
    {
        return $this->totalExcludingTaxes;
    }

    public function setTotalExcludingTaxes(float $totalExcludingTaxes): void
    {
        $this->totalExcludingTaxes = $totalExcludingTaxes;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?string $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getShippingState(): ?string
    {
        return $this->shippingState;
    }

    public function setShippingState(?string $shippingState): void
    {
        $this->shippingState = $shippingState;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getConfirmedAt(): ?DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTimeInterface $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function getShippedAt(): ?DateTimeInterface
    {
        return $this->shippedAt;
    }

    public function setShippedAt(?DateTimeInterface $shippedAt): void
    {
        $this->shippedAt = $shippedAt;
    }

    public function getDeliveredAt(): ?DateTimeInterface
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?DateTimeInterface $deliveredAt): void
    {
        $this->deliveredAt = $deliveredAt;
    }

    public function getCanceledAt(): ?DateTimeInterface
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?DateTimeInterface $canceledAt): void
    {
        $this->canceledAt = $canceledAt;
    }

    public function getRefusedAt(): ?DateTimeInterface
    {
        return $this->refusedAt;
    }

    public function setRefusedAt(?DateTimeInterface $refusedAt): void
    {
        $this->refusedAt = $refusedAt;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getShipmentAmount(): float
    {
        return $this->shipmentAmount;
    }

    public function setShipmentAmount(float $shipmentAmount): void
    {
        $this->shipmentAmount = $shipmentAmount;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): void
    {
        $this->paymentId = $paymentId;
    }
}
