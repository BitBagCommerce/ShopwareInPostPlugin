<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
