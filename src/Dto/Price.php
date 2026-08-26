<?php

declare(strict_types=1);

namespace App\Dto;

final class Price implements \JsonSerializable
{
    private ?int $amount;
    private ?float $displayPrice;
    private ?string $formattedDisplayPrice;
    private ?string $formattedDisplayUnitPrice;

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getDisplayPrice(): ?float
    {
        return $this->displayPrice;
    }

    public function setDisplayPrice(?float $displayPrice): void
    {
        $this->displayPrice = $displayPrice;
    }

    public function getFormattedDisplayPrice(): ?string
    {
        return $this->formattedDisplayPrice;
    }

    public function setFormattedDisplayPrice(?string $formattedDisplayPrice): void
    {
        $this->formattedDisplayPrice = $formattedDisplayPrice;
    }

    public function getFormattedDisplayUnitPrice(): ?string
    {
        return $this->formattedDisplayUnitPrice;
    }

    public function setFormattedDisplayUnitPrice(?string $formattedDisplayUnitPrice): void
    {
        $this->formattedDisplayUnitPrice = $formattedDisplayUnitPrice;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
