<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\QuantityStepsShippingCalculator;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->calculator = new QuantityStepsShippingCalculator($this->logger);
    $this->rule = [
        'levels' => [
            ['quantity_min' => 1, 'quantity_max' => 1, 'fdp_ht' => 83.33],
            ['quantity_min' => 2, 'quantity_max' => 2, 'fdp_ht' => 166.66],
            ['quantity_min' => 3, 'quantity_max' => 3, 'fdp_ht' => 249.99],
            ['quantity_min' => 4, 'quantity_max' => 999999, 'fdp_ht' => 0],
        ],
    ];
});

it('supports QUANTITY_STEPS type', function () {
    expect($this->calculator->supports('QUANTITY_STEPS'))->toBeTrue()
        ->and($this->calculator->supports('STANDARD'))->toBeFalse();
});

it('calculates shipping cost for one item', function () {
    $products = [['unitPrice' => 100.0, 'quantity' => 1]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(83.33)
        ->and($result->remainingForFranco)->toBe(0.0)
        ->and($result->type)->toBe('QUANTITY_STEPS');
});

it('calculates shipping cost for two items', function () {
    $products = [
        ['unitPrice' => 100.0, 'quantity' => 1],
        ['unitPrice' => 50.0, 'quantity' => 1],
    ];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(166.66)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping cost for three items', function () {
    $products = [['unitPrice' => 100.0, 'quantity' => 3]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(249.99);
});

it('calculates shipping cost for four or more items', function () {
    $products = [['unitPrice' => 100.0, 'quantity' => 4]];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0);
});

it('calculates shipping cost for multiple products with four total quantity', function () {
    $products = [
        ['unitPrice' => 50.0, 'quantity' => 2],
        ['unitPrice' => 30.0, 'quantity' => 2],
    ];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0);
});

it('calculates shipping cost for five items', function () {
    $products = [
        ['unitPrice' => 100.0, 'quantity' => 3],
        ['unitPrice' => 50.0, 'quantity' => 2],
    ];
    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0);
});
