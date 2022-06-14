<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Service;

use Shopware\Core\Framework\Context;

interface OrderServiceInterface
{
    public function saveTrackingNumberInOrder(
        array $shipments,
        array $orders,
        Context $context
    ): void;
}
