<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

final class BitBagShopwareInPostPlugin extends Plugin
{
    private EntityRepositoryInterface $shippingMethodRepository;

    private ShippingMethodPayloadFactoryInterface $shippingMethodFactory;

    private ShippingMethodFinderInterface $shippingMethodFinder;

    public function setShippingMethodRepository(EntityRepositoryInterface $shippingMethodRepository): void
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function setShippingMethodFactory(ShippingMethodPayloadFactoryInterface $shippingMethodFactory): void
    {
        $this->shippingMethodFactory = $shippingMethodFactory;
    }

    public function setShippingMethodFinder(ShippingMethodFinderInterface $shippingMethodFinder): void
    {
        $this->shippingMethodFinder = $shippingMethodFinder;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->createShippingMethod($activateContext->getContext());
        $this->toggleActiveShippingMethod(true, $activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->toggleActiveShippingMethod(false, $deactivateContext->getContext());
    }

    private function createShippingMethod(Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchIdsByShippingKey($context);
        if (0 !== $shippingMethod->getTotal()) {
            return;
        }

        $inPostShippingMethod = $this->shippingMethodFactory->create(
            ShippingMethodPayloadFactoryInterface::SHIPPING_KEY,
            $context
        );

        $this->shippingMethodRepository->create([$inPostShippingMethod], $context);
    }

    private function toggleActiveShippingMethod(bool $active, Context $context): void
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
