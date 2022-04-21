<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

final class CentimetersToMillimetersCalculator implements CentimetersToMillimetersCalculatorInterface
{
    public function calculate(float $value): float
    {
        return round($value * 10);
    }
}
