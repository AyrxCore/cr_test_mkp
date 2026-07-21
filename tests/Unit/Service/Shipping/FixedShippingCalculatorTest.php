<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\FixedShippingCalculator;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->calculator = new FixedShippingCalculator($this->logger);
    $this->rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 20, 'fdp_ht' => 5],
            ['franco_min_ht' => 20, 'franco_max_ht' => null, 'fdp_ht' => 3],
        ],
    ];
});

it('supports FIXED type', function () {
    expect($this->calculator->supports('FIXED'))->toBeTrue()
        ->and($this->calculator->supports('FREE'))->toBeFalse();
});

it('calculates shipping in first level', function () {
    $products = [['unitPrice' => 10.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(5.0)
        ->and($result->remainingForFranco)->toBe(10.0);
});

it('calculates shipping in last level with null franco_max', function () {
    $products = [['unitPrice' => 25.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(3.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping at boundary of last level', function () {
    $products = [['unitPrice' => 20.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(3.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
