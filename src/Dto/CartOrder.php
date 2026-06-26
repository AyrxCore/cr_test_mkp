<?php

declare(strict_types=1);

namespace App\Dto;

class CartOrder
{
    private ?Seller $seller = null;
    private ?float $totalPrice = null;
    private ?float $totalPriceWithTax = null;
    /** @var Product[] */
    private array $products = [];
    private ?ShippingCostResult $shippingCostResult = null;

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $sellerId): self
    {
        $this->seller = $sellerId;

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

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function addProduct(Product $product): self
    {
        if (!\in_array($product, $this->products, true)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products = \array_filter(
            $this->products,
            static fn (Product $item) => $item !== $product
        );

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

    public function getShippingCostResult(): ?ShippingCostResult
    {
        return $this->shippingCostResult;
    }

    public function setShippingCostResult(?ShippingCostResult $shippingCostResult): self
    {
        $this->shippingCostResult = $shippingCostResult;

        return $this;
    }
}
