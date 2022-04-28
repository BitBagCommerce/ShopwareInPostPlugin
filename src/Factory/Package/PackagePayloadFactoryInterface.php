<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface PackagePayloadFactoryInterface
{
    public function create(OrderEntity $order, Context $context): array;
}
