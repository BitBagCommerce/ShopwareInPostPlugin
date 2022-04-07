<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    public function calculate(OrderLineItemCollection $lineItems): float
    {
        $totalWeight = 0;

        foreach ($lineItems->getElements() as $item) {
            $product = $item->getProduct();
            if ($product) {
                $weight = $item->getQuantity() * $product->getWeight();
                $totalWeight += $weight;
            }
        }

        return $totalWeight;
    }
}
