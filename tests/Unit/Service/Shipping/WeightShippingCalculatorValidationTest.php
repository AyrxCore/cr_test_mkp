<?php

declare(strict_types=1);

use App\Exception\InvalidShippingConfigurationException;
use App\Service\Shipping\Calculator\WeightShippingCalculator;

beforeEach(function () {
    $this->calculator = new WeightShippingCalculator();
});

it('throws exception when weights array is empty', function () {
    $rule = ['weights' => []];
    $products = [['unitPrice' => 10.0, 'quantity' => 1, 'weight' => 5.0]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class, 'Shipping configuration for type "WEIGHT" has no weights defined');

it('throws exception when weights key is missing', function () {
    $rule = [];
    $products = [['unitPrice' => 10.0, 'quantity' => 1, 'weight' => 5.0]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class);
