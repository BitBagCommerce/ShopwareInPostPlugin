<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\ShippingAddress\ShippingAddressNotFoundException;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderShippingAddressResolver implements OrderShippingAddressResolverInterface
{
    private OrderDeliveryResolverInterface $orderDeliveryResolver;

    public function __construct(OrderDeliveryResolverInterface $orderDeliveryResolver)
    {
        $this->orderDeliveryResolver = $orderDeliveryResolver;
    }

    public function resolve(OrderEntity $order): OrderAddressEntity
    {
        $orderDelivery = $this->orderDeliveryResolver->resolve($order);

        $shippingOrderAddress = $orderDelivery->getShippingOrderAddress();

        if (null === $shippingOrderAddress) {
            throw new ShippingAddressNotFoundException('order.shippingAddressNotFound');
        }

        return $shippingOrderAddress;
    }
}
