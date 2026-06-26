<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

class FixedShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;
    use ResolvesShippingLevel;

    public function supports(string $type): bool
    {
        return $type === 'FIXED';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        $levels = $rule['levels'];
        $totalHt = $this->computeTotalHt($products);

        return $this->resolveLevel($levels, $totalHt, 'FIXED') ?? new ShippingCostResult(0.0, 0.0, 'FIXED');
    }
}
