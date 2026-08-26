<?php

declare(strict_types=1);

namespace App\Service\Shipping\Calculator;

use App\Dto\ShippingCostResult;
use App\Exception\InvalidShippingConfigurationException;
use Psr\Log\LoggerInterface;

class FixedShippingCalculator implements ShippingRuleCalculatorInterface
{
    use ComputesTotalHt;
    use ResolvesShippingLevel;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(string $type): bool
    {
        return $type === 'FIXED';
    }

    public function calculate(array $rule, array $products): ShippingCostResult
    {
        if (empty($rule['levels'])) {
            throw InvalidShippingConfigurationException::emptyLevels('FIXED');
        }

        $levels = $rule['levels'];
        $totalHt = $this->computeTotalHt($products);

        $result = $this->resolveLevel($levels, $totalHt, 'FIXED');

        if ($result === null) {
            $this->logger->warning('No matching level found for FIXED shipping', [
                'totalHt' => $totalHt,
                'levels' => $levels,
            ]);

            return new ShippingCostResult(0.0, 0.0, 'FIXED');
        }

        return $result;
    }
}
