<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;

interface OrderWeightCalculatorInterface
{
    public function calculate(OrderLineItemCollection $lineItems): float;
}
