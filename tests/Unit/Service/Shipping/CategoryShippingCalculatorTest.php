<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Shipping;

use App\Service\Shipping\Calculator\CategoryShippingCalculator;
use PHPUnit\Framework\TestCase;

class CategoryShippingCalculatorTest extends TestCase
{
    private CategoryShippingCalculator $calculator;

    private array $rule = [
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

    protected function setUp(): void
    {
        $this->calculator = new CategoryShippingCalculator();
    }

    public function testSupports(): void
    {
        self::assertTrue($this->calculator->supports('CATEGORY'));
        self::assertFalse($this->calculator->supports('STANDARD'));
    }

    public function testSingleCategoryBelowFranco(): void
    {
        $products = [
            ['unitPrice' => 10.0, 'quantity' => 2, 'shippingCategory' => 'test'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        self::assertSame(7.0, $result->shippingCost);
        self::assertSame(80.0, $result->remainingForFranco);
        self::assertSame('CATEGORY', $result->type);
    }

    public function testSingleCategoryFrancoReached(): void
    {
        $products = [
            ['unitPrice' => 50.0, 'quantity' => 2, 'shippingCategory' => 'test'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        self::assertSame(0.0, $result->shippingCost);
        self::assertSame(0.0, $result->remainingForFranco);
    }

    public function testMultipleCategoriesBothBelowFranco(): void
    {
        $products = [
            ['unitPrice' => 10.0, 'quantity' => 2, 'shippingCategory' => 'test'],
            ['unitPrice' => 20.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        // total global = 20 + 20 = 40 < 100
        // niveau dominant = test2 (fdp=8, franco=100)
        // fdp = 8, remaining = 100 - 40 = 60
        self::assertSame(8.0, $result->shippingCost);
        self::assertSame(60.0, $result->remainingForFranco);
    }

    public function testMultipleCategoriesGlobalFrancoReached(): void
    {
        $products = [
            ['unitPrice' => 50.0, 'quantity' => 1, 'shippingCategory' => 'test'],
            ['unitPrice' => 50.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        // total global = 50 + 50 = 100 >= 100 → franco atteint
        self::assertSame(0.0, $result->shippingCost);
        self::assertSame(0.0, $result->remainingForFranco);
    }

    public function testMultipleCategoriesOneFrancoReached(): void
    {
        $products = [
            ['unitPrice' => 50.0, 'quantity' => 2, 'shippingCategory' => 'test'],
            ['unitPrice' => 20.0, 'quantity' => 1, 'shippingCategory' => 'test2'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        // total global = 100 + 20 = 120 >= 100 → franco atteint
        self::assertSame(0.0, $result->shippingCost);
        self::assertSame(0.0, $result->remainingForFranco);
    }

    public function testProductWithUnknownCategoryIsIgnored(): void
    {
        $products = [
            ['unitPrice' => 10.0, 'quantity' => 1, 'shippingCategory' => 'unknown'],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        self::assertSame(0.0, $result->shippingCost);
        self::assertSame(0.0, $result->remainingForFranco);
    }

    public function testProductWithNullCategoryIsIgnored(): void
    {
        $products = [
            ['unitPrice' => 10.0, 'quantity' => 1, 'shippingCategory' => null],
        ];

        $result = $this->calculator->calculate($this->rule, $products);

        self::assertSame(0.0, $result->shippingCost);
        self::assertSame(0.0, $result->remainingForFranco);
    }
}
