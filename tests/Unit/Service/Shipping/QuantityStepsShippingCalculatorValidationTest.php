<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\QuantityStepsShippingCalculator;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->calculator = new QuantityStepsShippingCalculator($this->logger);
});

it('logs warning when no quantity level matches', function () {
    $rule = [
        'levels' => [
            ['quantity_min' => 10, 'quantity_max' => 20, 'fdp_ht' => 5],
        ],
    ];
    $products = [['unitPrice' => 10.0, 'quantity' => 5]];

    $this->logger->shouldReceive('warning')
        ->once()
        ->with('No matching quantity level found for QUANTITY_STEPS shipping', \Mockery::type('array'));

    $result = $this->calculator->calculate($rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('returns zero without warning when levels array is empty', function () {
    $rule = [];
    $products = [['unitPrice' => 10.0, 'quantity' => 5]];

    $this->logger->shouldReceive('warning')
        ->once()
        ->with('No matching quantity level found for QUANTITY_STEPS shipping', \Mockery::type('array'));

    $result = $this->calculator->calculate($rule, $products);

    expect($result->shippingCost)->toBe(0.0);
});
