<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface OrderWeightCalculatorInterface
{
    public function calculate(OrderEntity $order, Context $context): float;
}
