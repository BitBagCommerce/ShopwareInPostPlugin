<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class OrderValidator implements OrderValidatorInterface
{
    public function validate(OrderEntity $order, Context $context): bool
    {
        $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $inPostExtension) {
            throw new OrderException('order.extension.notFoundInPost');
        }

        /** @var array $orderInPostExtensionData = ['pointName' => 'string', 'packageId' => 'integer'] */
        $orderInPostExtensionData = $inPostExtension->getVars()['data'];

        if (null !== $orderInPostExtensionData['packageId']) {
            throw new PackageException('package.alreadyCreated');
        }

        return true;
    }
}
