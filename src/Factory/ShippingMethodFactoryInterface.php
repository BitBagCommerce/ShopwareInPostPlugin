<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Framework\Context;

interface ShippingMethodFactoryInterface
{
    public const SHIPPING_KEY = 'InPost';

    public function create(string $name, Context $context): array;
}
