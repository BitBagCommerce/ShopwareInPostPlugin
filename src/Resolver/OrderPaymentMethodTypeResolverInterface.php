<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderPaymentMethodTypeResolverInterface
{
    public function resolve(OrderEntity $order): string;
}
