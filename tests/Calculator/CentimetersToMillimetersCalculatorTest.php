<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Calculator;

use BitBag\ShopwareInPostPlugin\Calculator\CentimetersToMillimetersCalculator;
use PHPUnit\Framework\TestCase;

final class CentimetersToMillimetersCalculatorTest extends TestCase
{
    public function testIntValue(): void
    {
        $centimetersToMillimetersCalculator = new CentimetersToMillimetersCalculator();

        self::assertEquals(200, $centimetersToMillimetersCalculator->calculate(20));
    }

    public function testValueWithDecimals(): void
    {
        $centimetersToMillimetersCalculator = new CentimetersToMillimetersCalculator();

        self::assertEquals(255, $centimetersToMillimetersCalculator->calculate(25.5));
    }
}
