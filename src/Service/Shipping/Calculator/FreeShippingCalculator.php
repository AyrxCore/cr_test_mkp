<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

class FreeShippingCalculator implements ShippingRuleCalculatorInterface
{
    public function supports(string $type): bool
    {
        return $type === 'FREE';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        return new ShippingCostResult(0.0, 0.0, 'FREE');
    }
}
