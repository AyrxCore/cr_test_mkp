<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\PercentageShippingCalculator;
use PHPUnit\Framework\TestCase;

class PercentageShippingCalculatorTest extends TestCase
{
    private PercentageShippingCalculator $calculator;

    private array $rule = [
        'percentage' => 10,
    ];

    protected function setUp(): void
    {
        $this->calculator = new PercentageShippingCalculator();
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->calculator->supports('PERCENTAGE'));
        $this->assertFalse($this->calculator->supports('STANDARD'));
    }

    public function testCalculateWithSingleProduct(): void
    {
        $products = [['unitPrice' => 100.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(10.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
        $this->assertSame('PERCENTAGE', $result->type);
    }

    public function testCalculateWithMultipleProducts(): void
    {
        $products = [
            ['unitPrice' => 100.0, 'quantity' => 2],
            ['unitPrice' => 50.0, 'quantity' => 3],
        ];
        $result = $this->calculator->calculate($this->rule, $products);

        // Total: 200 + 150 = 350
        // 10% of 350 = 35.0
        $this->assertSame(35.0, $result->shippingCost);
    }

    public function testCalculateRounding(): void
    {
        $products = [['unitPrice' => 99.99, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        // 10% of 99.99 = 9.999 => rounded to 10.0
        $this->assertSame(10.0, $result->shippingCost);
    }

    public function testCalculateWithDecimalPercentage(): void
    {
        $rule = ['percentage' => 12.5];
        $products = [['unitPrice' => 80.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($rule, $products);

        // 12.5% of 80 = 10.0
        $this->assertSame(10.0, $result->shippingCost);
    }

    public function testCalculateZeroTotal(): void
    {
        $products = [['unitPrice' => 0.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
    }

    public function testCalculateEmptyProducts(): void
    {
        $products = [];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
    }
}
