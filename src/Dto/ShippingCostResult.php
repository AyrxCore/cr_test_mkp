<?php

declare(strict_types=1);

namespace App\Dto;

class ShippingCostResult
{
    public function __construct(
        public readonly float $shippingCost,
        public readonly float $remainingForFranco,
        public readonly string $type,
        public readonly float $maxTaxRate = 0.0,
    ) {
    }

    public function withMaxTaxRate(float $maxTaxRate): self
    {
        return new self(
            shippingCost: $this->shippingCost,
            remainingForFranco: $this->remainingForFranco,
            type: $this->type,
            maxTaxRate: $maxTaxRate,
        );
    }
}
