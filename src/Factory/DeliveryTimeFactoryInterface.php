<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Framework\Context;

interface DeliveryTimeFactoryInterface
{
    public function create(Context $context): array;
}
