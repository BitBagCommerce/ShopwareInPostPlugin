<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderDeliveryNotFoundException;
use BitBag\ShopwareInPostPlugin\Exception\ShippingAddress\ShippingAddressNotFoundException;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderDeliveryResolver implements OrderDeliveryResolverInterface
{
    public function resolve(OrderEntity $order): OrderDeliveryEntity
    {
        $orderDelivery = $order->getDeliveries();

        if (null === $orderDelivery) {
            throw new OrderDeliveryNotFoundException();
        }

        $firstOrderDelivery = $orderDelivery->first();

        if (null === $firstOrderDelivery) {
            throw new ShippingAddressNotFoundException('order.shippingAddressNotFound');
        }

        return $firstOrderDelivery;
    }
}
