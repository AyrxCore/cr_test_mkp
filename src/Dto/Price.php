<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class Price implements \JsonSerializable
{
    private ?int $amount;
    private ?float $displayPrice;
    private ?string $formattedDisplayPrice;
    private ?string $formattedDisplayUnitPrice;

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int|null
     */
    public function getDisplayPrice(): ?float
    {
        return $this->displayPrice;
    }

    /**
     * @param float|null $displayPrice
     */
    public function setDisplayPrice(?float $displayPrice): void
    {
        $this->displayPrice = $displayPrice;
    }

    /**
     * @return string|null
     */
    public function getFormattedDisplayPrice(): ?string
    {
        return $this->formattedDisplayPrice;
    }

    /**
     * @param string|null $formattedDisplayPrice
     */
    public function setFormattedDisplayPrice(?string $formattedDisplayPrice): void
    {
        $this->formattedDisplayPrice = $formattedDisplayPrice;
    }

    /**
     * @return string|null
     */
    public function getFormattedDisplayUnitPrice(): ?string
    {
        return $this->formattedDisplayUnitPrice;
    }

    /**
     * @param string|null $formattedDisplayUnitPrice
     */
    public function setFormattedDisplayUnitPrice(?string $formattedDisplayUnitPrice): void
    {
        $this->formattedDisplayUnitPrice = $formattedDisplayUnitPrice;
    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }

}
