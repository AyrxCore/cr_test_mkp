<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\StepsShippingCalculator;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->calculator = new StepsShippingCalculator($this->logger);
    $this->rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 50.45, 'fdp_ht' => 7],
            ['franco_min_ht' => 50.45, 'franco_max_ht' => 100.2, 'fdp_ht' => 3],
        ],
    ];
});

it('supports STEPS type', function () {
    expect($this->calculator->supports('STEPS'))->toBeTrue()
        ->and($this->calculator->supports('STANDARD'))->toBeFalse();
});

it('calculates shipping in first step', function () {
    $products = [['unitPrice' => 20.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(7.0)
        ->and($result->remainingForFranco)->toBe(30.45);
});

it('calculates shipping in second step', function () {
    $products = [['unitPrice' => 60.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(3.0)
        ->and($result->remainingForFranco)->toBe(40.2);
});

it('calculates shipping above all steps', function () {
    $products = [['unitPrice' => 150.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping at last step franco', function () {
    $products = [['unitPrice' => 100.2, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
