<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

/** @psalm-suppress PropertyNotSetInConstructor */
final class BitBagShopwareInPostPlugin extends Plugin
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private EntityRepositoryInterface $shippingMethodRepository;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private ShippingMethodFactoryInterface $shippingMethodFactory;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private ShippingMethodFinderInterface $shippingMethodFinder;

    public function setShippingMethodRepository(EntityRepositoryInterface $shippingMethodRepository): void
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function setShippingMethodFactory(ShippingMethodFactoryInterface $shippingMethodFactory): void
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
        $this->activateShippingMethod($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->deactivateShippingMethod($deactivateContext->getContext());
    }

    private function createShippingMethod(Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchIdsByShippingKey($context);
        if (0 !== $shippingMethod->getTotal()) {
            return;
        }

        $inPostShippingMethod = $this->shippingMethodFactory->create(
            ShippingMethodFactoryInterface::SHIPPING_KEY,
            $context
        );

        $this->shippingMethodRepository->create([$inPostShippingMethod], $context);
    }

    private function activateShippingMethod(Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchByShippingKey($context);
        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();
            if ($firstShippingMethod->getActive()) {
                return;
            }

            $this->shippingMethodRepository->update([
                [
                    'id' => $firstShippingMethod->getId(),
                    'active' => true,
                ],
            ], $context);
        }
    }

    private function deactivateShippingMethod(Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchByShippingKey($context);
        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();
            if (false === $firstShippingMethod->getActive()) {
                return;
            }

            $this->shippingMethodRepository->update([
                [
                    'id' => $firstShippingMethod->getId(),
                    'active' => false,
                ],
            ], $context);
        }
    }
}
