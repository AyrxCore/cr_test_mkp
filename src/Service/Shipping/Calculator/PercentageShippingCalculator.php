<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use App\Exception\InvalidShippingConfigurationException;

class PercentageShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;

    public function supports(string $type): bool
    {
        return $type === 'PERCENTAGE';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        if (!isset($rule['percentage'])) {
            throw InvalidShippingConfigurationException::missingPercentage();
        }

        $totalHt = $this->computeTotalHt($products);
        $percentage = (float) $rule['percentage'];

        $shippingCost = \round($totalHt * ($percentage / 100), 2);

        return new ShippingCostResult(
            $shippingCost,
            0.0,
            'PERCENTAGE'
        );
    }
}
