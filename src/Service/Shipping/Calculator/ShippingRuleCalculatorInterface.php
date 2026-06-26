<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

interface ShippingRuleCalculatorInterface
{
    public function supports(string $type): bool;

    /**
     * @param array<int, array{unitPrice: float, quantity: int, weight?: float}> $products
     */
    public function calculate(array $rule, array $products): ?ShippingCostResult;
}
