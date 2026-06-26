<?php

declare(strict_types=1);

use App\Entity\ShippingRule;
use App\Repository\ShippingRuleRepository;
use App\Service\Shipping\ShippingCostResolver;

\uses()->group('UnitShippingCostResolver');

\beforeEach(function () {
    $this->repository = Mockery::mock(ShippingRuleRepository::class);
    $this->resolver = new ShippingCostResolver($this->repository);
});

\it('returns fdp_ht when category matches', function () {
    $rule = new ShippingRule();
    $rule->setRule([
        'levels' => [
            ['category' => 'COMEBACK18', 'fdp_ht' => 18, 'franco_min_ht' => 0, 'franco_max_ht' => 1700],
            ['category' => 'COMEBACK20', 'fdp_ht' => 20, 'franco_min_ht' => 0, 'franco_max_ht' => 1700],
        ],
    ]);

    $this->repository->shouldReceive('findByPartnerAndType')
        ->with('partner-uuid', 'CATEGORY')
        ->andReturn($rule);

    \expect($this->resolver->resolveByCategory('partner-uuid', 'COMEBACK18'))->toBe(18.0);
    \expect($this->resolver->resolveByCategory('partner-uuid', 'COMEBACK20'))->toBe(20.0);
});

\it('returns null when category does not match any level', function () {
    $rule = new ShippingRule();
    $rule->setRule([
        'levels' => [
            ['category' => 'COMEBACK18', 'fdp_ht' => 18, 'franco_min_ht' => 0, 'franco_max_ht' => 1700],
        ],
    ]);

    $this->repository->shouldReceive('findByPartnerAndType')
        ->with('partner-uuid', 'CATEGORY')
        ->andReturn($rule);

    \expect($this->resolver->resolveByCategory('partner-uuid', 'UNKNOWN'))->toBeNull();
});

\it('returns null when no rule found for partner', function () {
    $this->repository->shouldReceive('findByPartnerAndType')
        ->with('unknown-partner', 'CATEGORY')
        ->andReturn(null);

    \expect($this->resolver->resolveByCategory('unknown-partner', 'COMEBACK18'))->toBeNull();
});

\it('returns null when level has no fdp_ht', function () {
    $rule = new ShippingRule();
    $rule->setRule([
        'levels' => [
            ['category' => 'COMEBACK18', 'franco_min_ht' => 0, 'franco_max_ht' => 1700],
        ],
    ]);

    $this->repository->shouldReceive('findByPartnerAndType')
        ->with('partner-uuid', 'CATEGORY')
        ->andReturn($rule);

    \expect($this->resolver->resolveByCategory('partner-uuid', 'COMEBACK18'))->toBeNull();
});

\it('returns null when rule has no levels', function () {
    $rule = new ShippingRule();
    $rule->setRule([]);

    $this->repository->shouldReceive('findByPartnerAndType')
        ->with('partner-uuid', 'CATEGORY')
        ->andReturn($rule);

    \expect($this->resolver->resolveByCategory('partner-uuid', 'COMEBACK18'))->toBeNull();
});
