<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\StandardShippingCalculator;

beforeEach(function () {
    $this->calculator = new StandardShippingCalculator();
    $this->rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 50.45, 'fdp_ht' => 7],
        ],
    ];
});

it('supports STANDARD type', function () {
    expect($this->calculator->supports('STANDARD'))->toBeTrue()
        ->and($this->calculator->supports('FREE'))->toBeFalse();
});

it('calculates shipping cost below franco', function () {
    $products = [['unitPrice' => 10.0, 'quantity' => 2]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(7.0)
        ->and($result->remainingForFranco)->toBe(30.45);
});

it('calculates shipping cost at franco', function () {
    $products = [['unitPrice' => 50.45, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping cost above franco', function () {
    $products = [['unitPrice' => 100.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
