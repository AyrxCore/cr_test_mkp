<?php

declare(strict_types=1);

namespace App\Service\Shipping;

use App\Repository\ShippingRuleRepository;

class ShippingCostResolver
{
    public function __construct(
        private readonly ShippingRuleRepository $shippingRuleRepository,
    ) {
    }

    public function resolveByCategory(string $partnerId, string $category): ?float
    {
        $rule = $this->shippingRuleRepository->findByPartnerAndType($partnerId, 'CATEGORY');

        if ($rule === null) {
            return null;
        }

        foreach ($rule->getRule()['levels'] ?? [] as $level) {
            if (($level['category'] ?? '') === $category) {
                return isset($level['fdp_ht']) ? (float) $level['fdp_ht'] : null;
            }
        }

        return null;
    }
}
