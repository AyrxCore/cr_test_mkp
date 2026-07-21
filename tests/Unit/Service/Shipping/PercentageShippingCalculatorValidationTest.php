<?php

declare(strict_types=1);

use App\Exception\InvalidShippingConfigurationException;
use App\Service\Shipping\Calculator\PercentageShippingCalculator;

beforeEach(function () {
    $this->calculator = new PercentageShippingCalculator();
});

it('throws exception when percentage field is missing', function () {
    $rule = [];
    $products = [['unitPrice' => 100.0, 'quantity' => 1]];

    $this->calculator->calculate($rule, $products);
})->throws(InvalidShippingConfigurationException::class, 'Shipping configuration for type "PERCENTAGE" is missing percentage field');
