<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\StepsShippingCalculator;
use PHPUnit\Framework\TestCase;

class StepsShippingCalculatorTest extends TestCase
{
    private StepsShippingCalculator $calculator;

    private array $rule = [
        'levels' => [
            ['franco_min_ht' => 0, 'franco_max_ht' => 50.45, 'fdp_ht' => 7],
            ['franco_min_ht' => 50.45, 'franco_max_ht' => 100.2, 'fdp_ht' => 3],
        ],
    ];

    protected function setUp(): void
    {
        $this->calculator = new StepsShippingCalculator();
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->calculator->supports('STEPS'));
        $this->assertFalse($this->calculator->supports('STANDARD'));
    }

    public function testInFirstStep(): void
    {
        $products = [['unitPrice' => 20.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(7.0, $result->shippingCost);
        $this->assertSame(30.45, $result->remainingForFranco);
    }

    public function testInSecondStep(): void
    {
        $products = [['unitPrice' => 60.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(3.0, $result->shippingCost);
        $this->assertSame(40.2, $result->remainingForFranco);
    }

    public function testAboveAllSteps(): void
    {
        $products = [['unitPrice' => 150.0, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }

    public function testAtLastStepFranco(): void
    {
        $products = [['unitPrice' => 100.2, 'quantity' => 1]];
        $result = $this->calculator->calculate($this->rule, $products);

        $this->assertSame(0.0, $result->shippingCost);
        $this->assertSame(0.0, $result->remainingForFranco);
    }
}
