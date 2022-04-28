<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface PackageApiServiceInterface
{
    public function createPackage(OrderEntity $order, Context $context): array;
}
