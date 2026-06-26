<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

trait ComputesTotalHt
{
    /**
     * @param array<int, array{unitPrice: float, quantity: int}> $products
     */
    private function computeTotalHt(array $products): float
    {
        return array_reduce($products, static fn (float $carry, array $p) => $carry + ($p['unitPrice'] * $p['quantity']), 0.0);
    }
}
