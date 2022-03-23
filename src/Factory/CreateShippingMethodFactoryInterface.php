<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Framework\Context;

interface CreateShippingMethodFactoryInterface
{
    public function create(string $name, Context $context): void;
}
