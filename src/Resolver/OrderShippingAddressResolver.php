<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderShippingAddressResolver implements OrderShippingAddressResolverInterface
{
    public function get(OrderEntity $order): OrderAddressEntity
    {
        $orderDelivery = $order->getDeliveries();

        if (null === $orderDelivery) {
            throw new OrderException('order.notFoundShippingAddress');
        }

        $firstOrderDelivery = $orderDelivery->first();

        if (null === $firstOrderDelivery) {
            throw new OrderException('order.notFoundShippingAddress');
        }

        $shippingOrderAddress = $firstOrderDelivery->getShippingOrderAddress();

        if (null === $shippingOrderAddress) {
            throw new OrderException('order.notFoundShippingAddress');
        }

        return $shippingOrderAddress;
    }
}
