<?php

declare(strict_types=1);

use App\Service\Shipping\Calculator\FreeShippingCalculator;

beforeEach(function () {
    $this->calculator = new FreeShippingCalculator();
});

it('supports FREE type', function () {
    expect($this->calculator->supports('FREE'))->toBeTrue()
        ->and($this->calculator->supports('STANDARD'))->toBeFalse();
});

it('shipping is always free', function () {
    $result = $this->calculator->calculate([], [['unitPrice' => 100.0, 'quantity' => 5]]);

    expect($result->shippingCost)->toBe(0.0)
        ->and($result->remainingForFranco)->toBe(0.0);
});
