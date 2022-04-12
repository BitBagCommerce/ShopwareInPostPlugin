<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use Shopware\Core\Checkout\Order\OrderEntity;

interface PackageApiServiceInterface
{
    public function createPackage(OrderEntity $order): array;
}
