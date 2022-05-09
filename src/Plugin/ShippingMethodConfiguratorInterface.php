<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use Shopware\Core\Framework\Context;

interface ShippingMethodConfiguratorInterface
{
    public function createShippingMethod(string $ruleId, Context $context): void;

    public function toggleActiveShippingMethod(bool $active, Context $context): void;
}
