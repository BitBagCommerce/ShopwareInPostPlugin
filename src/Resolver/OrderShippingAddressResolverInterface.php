<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderShippingAddressResolverInterface
{
    public function get(OrderEntity $order): OrderAddressEntity;
}
