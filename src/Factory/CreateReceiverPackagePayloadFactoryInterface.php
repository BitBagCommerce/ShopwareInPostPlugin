<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Checkout\Order\OrderEntity;

interface CreateReceiverPackagePayloadFactoryInterface
{
    public function create(OrderEntity $order): array;
}
