<?php

declare(strict_types=1);

namespace App\Dto;

class CartItem
{
    private ?float $unitPublicPrice = null;
    private ?float $unitPrice = null;
    private ?float $unitPriceWithTax = null;
    private ?int $quantity = null;
    private ?string $name = null;
    private ?string $sku = null;
    private ?string $packingType = null;
    private ?string $img = null;
    private ?string $offerPriceId = null;
    private ?string $action = null;
    /** @var CartItemOption[] */
    private array $options = [];
    private ?string $variantId = null;
    private ?int $franco = null;
    private ?int $shippingCost = null;

    public function getUnitPublicPrice(): ?float
    {
        return $this->unitPublicPrice;
    }

    public function setUnitPublicPrice(?float $unitPublicPrice): self
    {
        $this->unitPublicPrice = $unitPublicPrice;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getUnitPriceWithTax(): ?float
    {
        return $this->unitPriceWithTax;
    }

    public function setUnitPriceWithTax(?float $unitPriceWithTax): self
    {
        $this->unitPriceWithTax = $unitPriceWithTax;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPackingType(): ?string
    {
        return $this->packingType;
    }

    public function setPackingType(?string $packingType): self
    {
        $this->packingType = $packingType;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getOfferPriceId(): ?string
    {
        return $this->offerPriceId;
    }

    public function setOfferPriceId(?string $offerPriceId): self
    {
        $this->offerPriceId = $offerPriceId;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getVariantId(): ?string
    {
        return $this->variantId;
    }

    public function setVariantId(?string $variantId): self
    {
        $this->variantId = $variantId;

        return $this;
    }

    public function getFranco(): ?int
    {
        return $this->franco;
    }

    public function setFranco(?int $franco): self
    {
        $this->franco = $franco;

        return $this;
    }

    public function getShippingCost(): ?int
    {
        return $this->shippingCost;
    }

    public function setShippingCost(?int $shippingCost): self
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }
}
