<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface OrderFinderInterface
{
    public function getWithAssociations(string $orderId, Context $context): ?OrderEntity;
}
