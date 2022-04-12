<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderDeliveryResolverInterface
{
    public function resolve(OrderEntity $order): OrderDeliveryEntity;
}
