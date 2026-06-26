<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\WeightShippingCalculator;

uses()->group('UnitWeightShippingCalculator');

beforeEach(function () {
    $this->calculator = new WeightShippingCalculator();

    $this->ruleWithFranco = [
        'weights' => [
            ['weight_min' => 0, 'weight_max' => 1, 'fdp_ht' => 3, 'franco_min_ht' => 0, 'franco_max_ht' => 10],
            ['weight_min' => 1, 'weight_max' => 2, 'fdp_ht' => 6, 'franco_min_ht' => 0, 'franco_max_ht' => 10],
            ['weight_min' => 2, 'weight_max' => 3, 'fdp_ht' => 9, 'franco_min_ht' => 0, 'franco_max_ht' => 10],
        ],
    ];

    $this->ruleWithoutFranco = [
        'weights' => [
            ['weight_min' => 0, 'weight_max' => 10, 'fdp_ht' => 3, 'franco_min_ht' => null, 'franco_max_ht' => null],
            ['weight_min' => 10, 'weight_max' => 20, 'fdp_ht' => 6, 'franco_min_ht' => null, 'franco_max_ht' => null],
        ],
    ];
});

it('supports WEIGHT type and not others', function () {
    expect($this->calculator->supports('WEIGHT'))->toBeTrue();
    expect($this->calculator->supports('FREE'))->toBeFalse();
});

it('returns shipping cost when franco is not reached', function () {
    // poids total = 0.5kg, totalHt = 5€ < franco_max_ht 10€
    $products = [['unitPrice' => 5.0, 'quantity' => 1, 'weight' => 0.5]];
    $result = $this->calculator->calculate($this->ruleWithFranco, $products);

    expect($result->shippingCost)->toBe(3.0);
    expect($result->remainingForFranco)->toBe(5.0);
});

it('returns zero shipping cost when franco is reached', function () {
    // poids total = 0.5kg, totalHt = 15€ >= franco_max_ht 10€
    $products = [['unitPrice' => 15.0, 'quantity' => 1, 'weight' => 0.5]];
    $result = $this->calculator->calculate($this->ruleWithFranco, $products);

    expect($result->shippingCost)->toBe(0.0);
    expect($result->remainingForFranco)->toBe(0.0);
});

it('applies the correct weight slot for the second bracket', function () {
    // poids total = 1.5kg → slot 2, totalHt = 5€ < franco_max_ht 10€
    $products = [['unitPrice' => 5.0, 'quantity' => 1, 'weight' => 1.5]];
    $result = $this->calculator->calculate($this->ruleWithFranco, $products);

    expect($result->shippingCost)->toBe(6.0);
    expect($result->remainingForFranco)->toBe(5.0);
});

it('returns shipping cost without franco when franco fields are null', function () {
    // poids total = 5kg → slot 1, pas de franco
    $products = [['unitPrice' => 100.0, 'quantity' => 1, 'weight' => 5.0]];
    $result = $this->calculator->calculate($this->ruleWithoutFranco, $products);

    expect($result->shippingCost)->toBe(3.0);
    expect($result->remainingForFranco)->toBe(0.0);
});

it('applies the second weight slot when franco is not configured', function () {
    // poids total = 15kg → slot 2
    $products = [['unitPrice' => 10.0, 'quantity' => 1, 'weight' => 15.0]];
    $result = $this->calculator->calculate($this->ruleWithoutFranco, $products);

    expect($result->shippingCost)->toBe(6.0);
    expect($result->remainingForFranco)->toBe(0.0);
});

it('returns null when no product has a weight', function () {
    $products = [
        ['unitPrice' => 10.0, 'quantity' => 2],
        ['unitPrice' => 5.0, 'quantity' => 1, 'weight' => null],
    ];
    $result = $this->calculator->calculate($this->ruleWithFranco, $products);

    expect($result)->toBeNull();
});

it('ignores products without weight for weight sum but includes their price for franco', function () {
    // produit sans poids ignoré pour le calcul du poids, mais son prix compte pour le totalHt
    $products = [
        ['unitPrice' => 5.0, 'quantity' => 1, 'weight' => 0.5],
        ['unitPrice' => 3.0, 'quantity' => 1],
    ];
    $result = $this->calculator->calculate($this->ruleWithFranco, $products);

    // poids = 0.5kg → slot 1 (fdp=3), totalHt = 8€ < 10€
    expect($result)->not->toBeNull();
    expect($result->shippingCost)->toBe(3.0);
    expect($result->remainingForFranco)->toBe(2.0);
});
