<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\StandardShippingCalculator;
use PHPUnit\Framework\TestCase;

class StandardShippingCalculatorTest extends TestCase
{
    private StandardShippingCalculator $calculator;

    private array $rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 50.45, 'fdp_ht' => 7],
        ],
    ];

    protected function setUp(): void
    {
        $this->calculator = new StandardShippingCalculator();
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->calculator->supports('STANDARD'));
        $this->assertFalse($this->calculator->supports('FREE'));
    }

    public function testBelowFranco(): void
    {
        $products = [['unitPrice' => 10.0, 'quantity' => 2]]; // 20€
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(7.0, $result->shippingCost);
        $this->assertSame(30.45, $result->remainingForFranco);
    }

    public function testAtFranco(): void
    {
        $products = [['unitPrice' => 50.45, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }

    public function testAboveFranco(): void
    {
        $products = [['unitPrice' => 100.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }
}
