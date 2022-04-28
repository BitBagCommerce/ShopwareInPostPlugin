<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use Shopware\Core\Framework\Context;

interface ShippingMethodConfiguratorInterface
{
    public function createShippingMethod(string $ruleId, Context $context): void;

    public function toggleActiveShippingMethod(bool $active, Context $context): void;
}
