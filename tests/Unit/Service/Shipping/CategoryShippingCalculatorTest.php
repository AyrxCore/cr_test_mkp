<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\CategoryShippingCalculator;

beforeEach(function () {
    $this->calculator = new CategoryShippingCalculator();
    $this->rule = [
        'levels' => [
            [
                'category' => 'test',
                'fdp_ht' => 7,
                'franco_max_ht' => 100,
                'franco_min_ht' => 0,
            ],
            [
                'category' => 'test2',
                'fdp_ht' => 8,
                'franco_max_ht' => 100,
                'franco_min_ht' => 0,
            ],
        ],
    ];
});

it('supports CATEGORY type', function () {
    expect($this->calculator->supports('CATEGORY'))->toBeTrue()
        ->and($this->calculator->supports('STANDARD'))->toBeFalse();
});

it('calculates shipping for single category below franco', function () {
    $products = [
        ['unitPrice' => 10.0, 'quantity' => 2, 'shippingCategory' => 'test'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(7.0)
        ->and($result->remainingForFranco)->toBe(80.0)
        ->and($result->type)->toBe('CATEGORY');
});

it('calculates shipping for single category franco reached', function () {
    $products = [
        ['unitPrice' => 50.0, 'quantity' => 2, 'shippingCategory' => 'test'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping for multiple categories both below franco', function () {
    $products = [
        ['unitPrice' => 10.0, 'quantity' => 2, 'shippingCategory' => 'test'],
        ['unitPrice' => 20.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(8.0)
        ->and($result->remainingForFranco)->toBe(60.0);
});

it('calculates shipping for multiple categories global franco reached', function () {
    $products = [
        ['unitPrice' => 50.0, 'quantity' => 1, 'shippingCategory' => 'test'],
        ['unitPrice' => 50.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('calculates shipping for multiple categories one franco reached', function () {
    $products = [
        ['unitPrice' => 50.0, 'quantity' => 2, 'shippingCategory' => 'test'],
        ['unitPrice' => 20.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('ignores product with unknown category', function () {
    $products = [
        ['unitPrice' => 10.0, 'quantity' => 1, 'shippingCategory' => 'unknown'],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});

it('ignores product with null category', function () {
    $products = [
        ['unitPrice' => 10.0, 'quantity' => 1, 'shippingCategory' => null],
    ];

    $result = $this->calculator->calculate($this->rule, $products);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
