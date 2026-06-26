<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\FixedShippingCalculator;
use PHPUnit\Framework\TestCase;

class FixedShippingCalculatorTest extends TestCase
{
    private FixedShippingCalculator $calculator;

    private array $rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 20, 'fdp_ht' => 5],
            ['franco_min_ht' => 20, 'franco_max_ht' => null, 'fdp_ht' => 3],
        ],
    ];

    protected function setUp(): void
    {
        $this->calculator = new FixedShippingCalculator();
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->calculator->supports('FIXED'));
        $this->assertFalse($this->calculator->supports('FREE'));
    }

    public function testInFirstLevel(): void
    {
        $products = [['unitPrice' => 10.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(5.0, $result->shippingCost);
        $this->assertSame(10.0, $result->remainingForFranco);
    }

    public function testInLastLevelWithNullFrancoMax(): void
    {
        $products = [['unitPrice' => 25.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(3.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }

    public function testAtBoundaryOfLastLevel(): void
    {
        $products = [['unitPrice' => 20.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(3.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }
}
