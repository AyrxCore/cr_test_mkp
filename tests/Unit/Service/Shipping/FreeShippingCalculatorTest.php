<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\FreeShippingCalculator;
use PHPUnit\Framework\TestCase;

class FreeShippingCalculatorTest extends TestCase
{
    private FreeShippingCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new FreeShippingCalculator();
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->calculator->supports('FREE'));
        $this->assertFalse($this->calculator->supports('STANDARD'));
    }

    public function testAlwaysFree(): void
    {
        $result = $this->calculator->calculate([], [['unitPrice' => 100.0, 'quantity' => 5]]);

        $this->assertSame(0.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }
}
