<?php

declare(strict_types=1);

namespace App\Service\Shipping;

use App\Dto\ShippingCostResult;
use App\Repository\PartnerRepository;
use App\Repository\ShippingRuleRepository;
use App\Service\Shipping\Calculator\ShippingRuleCalculatorInterface;

class ShippingCostService
{
    /**
     * @param iterable<ShippingRuleCalculatorInterface> $calculators
     */
    public function __construct(
        private readonly ShippingRuleRepository $shippingRuleRepository,
        private readonly PartnerRepository $partnerRepository,
        private readonly iterable $calculators,
    ) {
    }

    /**
     * @param array<int, array{unitPrice: float, quantity: int, weight?: float}> $products
     */
    public function computeForPartnerDjustId(string $partnerId, array $products): ?ShippingCostResult
    {
        $partner = $this->partnerRepository->find($partnerId);

        if ($partner === null) {
            return null;
        }

        $shippingRule = $this->shippingRuleRepository->findOneBy(['partner' => $partner]);

        if ($shippingRule === null) {
            return null;
        }

        foreach ($this->calculators as $calculator) {
            if ($calculator->supports($shippingRule->getType())) {
                return $calculator->calculate($shippingRule->getRule(), $products);
            }
        }

        return null;
    }
}
