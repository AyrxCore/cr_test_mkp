<?php

declare(strict_types=1);

use App\Exception\InvalidShippingConfigurationException;
use App\Service\Shipping\Calculator\StandardShippingCalculator;

beforeEach(function () {
    $this->calculator = new StandardShippingCalculator();
});

it('throws exception when levels array is empty', function () {
    $rule = ['levels' => []];
    $products = [['unitPrice' => 10.0, 'quantity' => 1]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class, 'Shipping configuration for type "STANDARD" has no levels defined');

it('throws exception when levels key is missing', function () {
    $rule = [];
    $products = [['unitPrice' => 10.0, 'quantity' => 1]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class);
