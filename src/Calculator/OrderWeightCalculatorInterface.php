<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderWeightCalculatorInterface
{
    public function calculate(OrderEntity $order): float;
}
