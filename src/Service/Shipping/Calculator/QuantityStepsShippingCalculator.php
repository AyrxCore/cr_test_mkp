<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use Psr\Log\LoggerInterface;

class QuantityStepsShippingCalculator implements ShippingRuleCalculatorInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(string $type): bool
    {
        return $type === 'QUANTITY_STEPS';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        $totalQuantity = \array_reduce($products, static fn (int $carry, array $p) => $carry + $p['quantity'], 0);

        $levels = $rule['levels'] ?? [];

        foreach ($levels as $level) {
            if ($totalQuantity >= ($level['quantity_min'] ?? 0) && $totalQuantity <= ($level['quantity_max'] ?? \PHP_INT_MAX)) {
                $fdpHt = (float) ($level['fdp_ht'] ?? 0);

                return new ShippingCostResult(
                    $fdpHt,
                    0.0,
                    'QUANTITY_STEPS'
                );
            }
        }

        $this->logger->warning('No matching quantity level found for QUANTITY_STEPS shipping', [
            'totalQuantity' => $totalQuantity,
            'levels' => $levels,
        ]);

        return new ShippingCostResult(0.0, 0.0, 'QUANTITY_STEPS');
    }
}
