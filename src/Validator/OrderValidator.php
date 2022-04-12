<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Exception\ShippingMethodException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class OrderValidator implements OrderValidatorInterface
{
    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(OrderWeightCalculatorInterface $orderWeightCalculator)
    {
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function validate(OrderEntity $order, Context $context): bool
    {
        $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $inPostExtension) {
            throw new OrderException('order.extension.notFoundInPost');
        }

        /** @var array $orderInPostExtensionData = ['pointName' => 'string', 'packageId' => 'integer'] */
        $orderInPostExtensionData = $inPostExtension->getVars()['data'];

        if (null === $orderInPostExtensionData['pointName']) {
            throw new PackageException('package.notFoundPointName');
        }

        if (null !== $orderInPostExtensionData['packageId']) {
            throw new PackageException('package.alreadyCreated');
        }

        if (null === $orderDelivery = $order->getDeliveries()) {
            throw new OrderException('order.shippingMethodNotFound');
        }

        $firstOrderDelivery = $orderDelivery->first();

        if (null === $firstOrderDelivery) {
            throw new OrderException('order.shippingMethodNotFound');
        }

        $shippingMethod = $firstOrderDelivery->getShippingMethod();

        if (null === $shippingMethod) {
            throw new PackageException('order.notFoundShippingMethod');
        }

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $shippingMethod->getName()) {
            throw new ShippingMethodException(str_replace(
                '%shippingMethod%',
                ShippingMethodPayloadFactoryInterface::SHIPPING_KEY,
                'shippingMethod.notEquals'
            ));
        }

        return true;
    }
}
