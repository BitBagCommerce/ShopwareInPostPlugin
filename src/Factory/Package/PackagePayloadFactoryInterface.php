<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use Shopware\Core\Checkout\Order\OrderEntity;

interface PackagePayloadFactoryInterface
{
    public function create(OrderEntity $order): array;
}
