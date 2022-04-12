<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Exception\ShippingMethodException;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Resolver\OrderDeliveryResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class InpostShippingMethodValidator implements InpostShippingMethodValidatorInterface
{
    private OrderDeliveryResolverInterface $orderDeliveryResolver;

    public function __construct(OrderDeliveryResolverInterface $orderDeliveryResolver)
    {
        $this->orderDeliveryResolver = $orderDeliveryResolver;
    }

    public function validate(OrderEntity $order): bool
    {
        $orderDelivery = $this->orderDeliveryResolver->resolve($order);

        $shippingMethod = $orderDelivery->getShippingMethod();

        if (null === $shippingMethod) {
            throw new PackageException('order.shippingMethodNotFound');
        }

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $shippingMethod->getName()) {
            throw new ShippingMethodException('shippingMethod.notInpost');
        }

        return true;
    }
}
