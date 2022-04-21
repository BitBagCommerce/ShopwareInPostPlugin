<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

interface CentimetersToMillimetersCalculatorInterface
{
    public function calculate(int $value): int;
}
