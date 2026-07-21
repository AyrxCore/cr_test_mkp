<?php

declare(strict_types=1);

use App\Exception\InvalidShippingConfigurationException;
use App\Service\Shipping\Calculator\FixedShippingCalculator;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->calculator = new FixedShippingCalculator($this->logger);
});

it('throws exception when levels array is empty', function () {
    $rule = ['levels' => []];
    $products = [['unitPrice' => 10.0, 'quantity' => 1]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class, 'Shipping configuration for type "FIXED" has no levels defined');

it('logs warning and returns zero when no level matches', function () {
    $rule = [
        'levels' => [
            ['franco_min_ht' => 100, 'franco_max_ht' => 200, 'fdp_ht' => 5],
        ],
    ];
    $products = [['unitPrice' => 50.0, 'quantity' => 1]];

    $this->logger->shouldReceive('warning')
        ->once()
        ->with('No matching level found for FIXED shipping', \Mockery::type('array'));

    $result = $this->calculator->calculate($rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
