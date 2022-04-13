<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderExtensionDataResolverInterface
{
    /** @return array ['id' => 'integer', 'pointName' => 'string', 'packageId' => 'integer'] */
    public function resolve(OrderEntity $order): array;
}
