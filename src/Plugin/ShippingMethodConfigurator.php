<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class ShippingMethodConfigurator implements ShippingMethodConfiguratorInterface
{
    private ShippingMethodFinderInterface $shippingMethodFinder;

    private ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory;

    private EntityRepositoryInterface $shippingMethodRepository;

    public function __construct(
        ShippingMethodFinderInterface $shippingMethodFinder,
        ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory,
        EntityRepositoryInterface $shippingMethodRepository
    ) {
        $this->shippingMethodFinder = $shippingMethodFinder;
        $this->shippingMethodPayloadFactory = $shippingMethodPayloadFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function createShippingMethod(string $ruleId, Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchIdsByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            return;
        }

        $inPostShippingMethod = $this->shippingMethodPayloadFactory->create(
            ShippingMethodPayloadFactoryInterface::SHIPPING_KEY,
            $ruleId,
            $context
        );

        $this->shippingMethodRepository->create([$inPostShippingMethod], $context);
    }

    public function toggleActiveShippingMethod(bool $active, Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();

            $this->shippingMethodRepository->update([
                [
                    'id' => $firstShippingMethod->getId(),
                    'active' => $active,
                ],
            ], $context);
        }
    }
}
