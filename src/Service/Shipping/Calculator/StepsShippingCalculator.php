<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use App\Exception\InvalidShippingConfigurationException;
use Psr\Log\LoggerInterface;

class StepsShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;
    use ResolvesShippingLevel;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(string $type): bool
    {
        return $type === 'STEPS';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        if (empty($rule['levels'])) {
            throw InvalidShippingConfigurationException::emptyLevels('STEPS');
        }

        $levels = $rule['levels'];
        $totalHt = $this->computeTotalHt($products);
        $lastLevel = $levels[\array_key_last($levels)];

        if ($totalHt >= (float) $lastLevel['franco_max_ht']) {
            return new ShippingCostResult(0.0, 0.0, 'STEPS');
        }

        $result = $this->resolveLevel($levels, $totalHt, 'STEPS');

        if ($result === null) {
            $this->logger->warning('No matching level found for STEPS shipping', [
                'totalHt' => $totalHt,
                'levels' => $levels,
            ]);

            return new ShippingCostResult(0.0, 0.0, 'STEPS');
        }

        return $result;
    }
}
