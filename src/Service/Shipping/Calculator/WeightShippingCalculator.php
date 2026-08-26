<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use App\Exception\InvalidShippingConfigurationException;

class WeightShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;

    public function supports(string $type): bool
    {
        return $type === 'WEIGHT';
    }

    public function calculate(array $rule, array $products): ?ShippingCostResult
    {
        if (empty($rule['weights'])) {
            throw InvalidShippingConfigurationException::emptyWeights();
        }

        $productsWithWeight = \array_filter($products, static fn (array $p) => isset($p['weight']) && $p['weight'] !== null);

        if (empty($productsWithWeight)) {
            return null;
        }

        $totalWeight = \array_reduce($productsWithWeight, static fn (float $carry, array $p) => $carry + ((float) $p['weight'] * $p['quantity']), 0.0);

        $matchedSlot = null;
        foreach ($rule['weights'] as $slot) {
            if ($totalWeight >= (float) $slot['weight_min'] && $totalWeight <= (float) $slot['weight_max']) {
                $matchedSlot = $slot;
                break;
            }
        }

        if ($matchedSlot === null) {
            $matchedSlot = $rule['weights'][\array_key_last($rule['weights'])];
        }

        $fdpHt = (float) $matchedSlot['fdp_ht'];
        $francoMax = isset($matchedSlot['franco_max_ht']) && $matchedSlot['franco_max_ht'] !== null
            ? (float) $matchedSlot['franco_max_ht']
            : null;

        if ($francoMax === null) {
            return new ShippingCostResult($fdpHt, 0.0, 'WEIGHT');
        }

        $totalHt = $this->computeTotalHt($products);

        if ($totalHt >= $francoMax) {
            return new ShippingCostResult(0.0, 0.0, 'WEIGHT');
        }

        return new ShippingCostResult($fdpHt, \round($francoMax - $totalHt, 2), 'WEIGHT');
    }
}
