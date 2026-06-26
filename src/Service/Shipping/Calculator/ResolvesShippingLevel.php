<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

trait ResolvesShippingLevel
{
    /**
     * @param array<int, array{franco_min_ht: mixed, franco_max_ht: mixed, fdp_ht: mixed}> $levels
     */
    private function resolveLevel(array $levels, float $totalHt, string $type): ?ShippingCostResult
    {
        foreach ($levels as $level) {
            $francoMin = (float) $level['franco_min_ht'];
            $francoMax = $level['franco_max_ht'] !== null ? (float) $level['franco_max_ht'] : null;

            if ($francoMax === null) {
                if ($totalHt >= $francoMin) {
                    return new ShippingCostResult((float) $level['fdp_ht'], 0.0, $type);
                }
            } elseif ($totalHt >= $francoMin && $totalHt < $francoMax) {
                return new ShippingCostResult(
                    (float) $level['fdp_ht'],
                    round($francoMax - $totalHt, 2),
                    $type,
                );
            }
        }

        return null;
    }
}
