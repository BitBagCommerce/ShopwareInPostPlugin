<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderExtensionNotFoundException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderExtensionDataResolver implements OrderExtensionDataResolverInterface
{
    public function resolve(OrderEntity $order): array
    {
        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new OrderExtensionNotFoundException('order.extension.notFound');
        }

        return $orderExtension->getVars()['data'];
    }
}
