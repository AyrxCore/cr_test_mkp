<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;

class CategoryShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;

    public function supports(string $type): bool
    {
        return $type === 'CATEGORY';
    }

    /**
     * @param array<int, array{unitPrice: float, quantity: int, shippingCategory?: string|null}> $products
     */
    public function calculate(array $rule, array $products): ShippingCostResult
    {
        $levels = $rule['levels'];
        $levelsByCategory = [];
        foreach ($levels as $level) {
            $levelsByCategory[$level['category']] = $level;
        }

        $matchedLevels = [];
        $eligibleProducts = [];
        foreach ($products as $product) {
            $category = $product['shippingCategory'] ?? null;
            if ($category !== null && isset($levelsByCategory[$category])) {
                $matchedLevels[$category] = $levelsByCategory[$category];
                $eligibleProducts[] = $product;
            }
        }

        if (empty($matchedLevels)) {
            return new ShippingCostResult(0.0, 0.0, 'CATEGORY');
        }

        $dominantLevel = null;
        foreach ($matchedLevels as $level) {
            if ($dominantLevel === null || (float) $level['fdp_ht'] > (float) $dominantLevel['fdp_ht']) {
                $dominantLevel = $level;
            }
        }

        $totalHt = $this->computeTotalHt($eligibleProducts);
        $francoMax = (float) $dominantLevel['franco_max_ht'];
        $fdp = (float) $dominantLevel['fdp_ht'];

        if ($totalHt >= $francoMax) {
            return new ShippingCostResult(0.0, 0.0, 'CATEGORY');
        }

        $remainingForFranco = round($francoMax - $totalHt, 2);

        return new ShippingCostResult(
            round($fdp, 2),
            $remainingForFranco,
            'CATEGORY',
        );
    }
}
