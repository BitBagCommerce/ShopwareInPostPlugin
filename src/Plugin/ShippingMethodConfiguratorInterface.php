<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use Shopware\Core\Framework\Context;

interface ShippingMethodConfiguratorInterface
{
    public function createShippingMethod(string $ruleId, Context $context): void;

    public function toggleActiveShippingMethod(bool $active, Context $context): void;
}
