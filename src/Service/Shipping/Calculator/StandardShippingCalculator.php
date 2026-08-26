<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use App\Exception\InvalidShippingConfigurationException;

class StandardShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;

    public function supports(string $type): bool
    {
        return $type === 'STANDARD';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        if (empty($rule['levels'])) {
            throw InvalidShippingConfigurationException::emptyLevels('STANDARD');
        }

        $level = $rule['levels'][0];
        $totalHt = $this->computeTotalHt($products);
        $francoMax = (float) $level['franco_max_ht'];

        if ($totalHt >= $francoMax) {
            return new ShippingCostResult(0.0, 0.0, 'STANDARD');
        }

        return new ShippingCostResult(
            (float) $level['fdp_ht'],
            \round($francoMax - $totalHt, 2),
            'STANDARD',
        );
    }
}
