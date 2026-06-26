<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\Provider\CartProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/cart',
        ),
    ],
    provider: CartProvider::class,
)]
class Cart
{
    private ?string $id = null;
    private ?int $productCount = null;
    private ?float $totalPrice = null;
    private ?float $totalPriceWithTax = null;
    private ?string $currency = null;
    /** @var CartOrder[] */
    private array $cartOrders = [];
    private ?string $shippingAddressExternalId = null;
    private ?string $billingAddressExternalId = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProductCount(): ?int
    {
        return $this->productCount;
    }

    public function setProductCount(?int $productCount): self
    {
        $this->productCount = $productCount;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalPriceWithTax(): ?float
    {
        return $this->totalPriceWithTax;
    }

    public function setTotalPriceWithTax(?float $totalPriceWithTax): self
    {
        $this->totalPriceWithTax = $totalPriceWithTax;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCartOrders(): array
    {
        return $this->cartOrders;
    }

    public function setCartOrders(array $cartOrder): self
    {
        $this->cartOrders = $cartOrder;

        return $this;
    }

    public function addCartOrder(CartOrder $cartOrder): self
    {
        if (!\in_array($cartOrder, $this->cartOrders, true)) {
            $this->cartOrders[] = $cartOrder;
        }

        return $this;
    }

    public function removeCartOrder(CartOrder $cartOrder): self
    {
        $this->cartOrders = \array_filter(
            $this->cartOrders,
            static fn (CartOrder $order) => $order !== $cartOrder
        );

        return $this;
    }

    public function getShippingAddressExternalId(): ?string
    {
        return $this->shippingAddressExternalId;
    }

    public function setShippingAddressExternalId(?string $shippingAddressExternalId): self
    {
        $this->shippingAddressExternalId = $shippingAddressExternalId;

        return $this;
    }

    public function getBillingAddressExternalId(): ?string
    {
        return $this->billingAddressExternalId;
    }

    public function setBillingAddressExternalId(?string $billingAddressExternalId): self
    {
        $this->billingAddressExternalId = $billingAddressExternalId;

        return $this;
    }
}
