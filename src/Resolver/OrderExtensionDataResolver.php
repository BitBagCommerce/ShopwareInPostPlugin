<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
