<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

class StepsShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;
    use ResolvesShippingLevel;

    public function supports(string $type): bool
    {
        return $type === 'STEPS';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        $levels = $rule['levels'];
        $totalHt = $this->computeTotalHt($products);
        $lastLevel = $levels[array_key_last($levels)];

        if ($totalHt >= (float) $lastLevel['franco_max_ht']) {
            return new ShippingCostResult(0.0, 0.0, 'STEPS');
        }

        return $this->resolveLevel($levels, $totalHt, 'STEPS') ?? new ShippingCostResult(0.0, 0.0, 'STEPS');
    }
}
