<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\OrderProvider;

#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\\d+'],
            openapi: new Operation(
                summary: 'Récupérer un historique par son identifiant',
                description: 'Cette opération permet de récupérer les informations d\'une commande en spécifiant son identifiant unique.'
            )
        ),
        new GetCollection(
            openapi: new Operation(
                summary: 'Liste des commandes',
                description: 'Cette opération permet de récupérer la liste des commandes associées à un acheteur.'
            ),
        ),
    ],
    provider: OrderProvider::class
)]
final class Order
{
    /**
     * Shipping statuses.
     */
    public const string SHIPPING_PENDING = 'pending';
    public const string SHIPPING_PREPARATION = 'preparation';
    public const string SHIPPING_READY = 'ready';
    public const string SHIPPING_PARTIALLY_SHIPPED = 'partially_shipped';
    public const string SHIPPING_SHIPPED = 'shipped';
    public const string SHIPPING_DELIVERED = 'delivered';
    public const string SHIPPING_RETURNED = 'returned';
    public const string SHIPPING_CANCELLED = 'cancelled';

    #[ApiProperty(identifier: true)]
    private ?int $id = null;
    private string $orderNumber;
    private array $items = [];
    private int $productCount = 0;
    private float $shipmentAmount = 0;
    private float $total = 0;
    private float $totalExcludingTaxes = 0;
    private ?string $billingAddress = null;
    private ?string $shippingAddress = null;
    private ?string $shippingState = null;
    private ?string $shippingTrackingUrl = null;
    private ?string $invoiceUrl = null;
    private array $orderInvoiceLinks = [];
    private array $orderPartners = [];
    private ?\DateTimeInterface $createdAt = null;
    private ?\DateTimeInterface $updatedAt = null;
    private ?\DateTimeInterface $confirmedAt = null;
    /** Affiché dans OrderComponent (badge "Expédiée") mais pas l'information dans l'API */
    private ?\DateTimeInterface $shippedAt = null;
    /** Affiché dans OrderComponent (badge "Livrée") mais pas l'information dans l'API */
    private ?\DateTimeInterface $deliveredAt = null;

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

    public function getShippingTrackingUrl(): ?string
    {
        return $this->shippingTrackingUrl;
    }

    public function setShippingTrackingUrl(?string $shippingTrackingUrl): void
    {
        $this->shippingTrackingUrl = $shippingTrackingUrl;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function getShippedAt(): ?\DateTimeInterface
    {
        return $this->shippedAt;
    }

    public function setShippedAt(?\DateTimeInterface $shippedAt): void
    {
        $this->shippedAt = $shippedAt;
    }

    public function getDeliveredAt(): ?\DateTimeInterface
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?\DateTimeInterface $deliveredAt): void
    {
        $this->deliveredAt = $deliveredAt;
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

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function setProductCount(int $productCount): void
    {
        $this->productCount = $productCount;
    }

    public function getShipmentAmount(): float
    {
        return $this->shipmentAmount;
    }

    public function setShipmentAmount(float $shipmentAmount): void
    {
        $this->shipmentAmount = $shipmentAmount;
    }

    public function getInvoiceUrl(): ?string
    {
        return $this->invoiceUrl;
    }

    public function setInvoiceUrl(?string $invoiceUrl): void
    {
        $this->invoiceUrl = $invoiceUrl;
    }

    public function getOrderInvoiceLinks(): array
    {
        return $this->orderInvoiceLinks;
    }

    public function setOrderInvoiceLinks(array $orderInvoiceLinks): void
    {
        $this->orderInvoiceLinks = $orderInvoiceLinks;
    }

    public function getOrderPartners(): array
    {
        return $this->orderPartners;
    }

    public function setOrderPartners(array $orderPartners): void
    {
        $this->orderPartners = $orderPartners;
    }
}
