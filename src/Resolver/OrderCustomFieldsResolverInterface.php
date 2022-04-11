<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderCustomFieldsResolverInterface
{
    public function resolve(OrderEntity $order): array;
}
