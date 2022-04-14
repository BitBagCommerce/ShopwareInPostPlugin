<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\Order\ExtensionNotFoundException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderExtensionDataResolver implements OrderExtensionDataResolverInterface
{
    public function resolve(OrderEntity $order): array
    {
        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new ExtensionNotFoundException('order.extension.notFound');
        }

        return $orderExtension->getVars()['data'];
    }
}
