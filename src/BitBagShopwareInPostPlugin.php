<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Factory\CreateShippingMethodFactory;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodFactoryInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

/** @psalm-suppress PropertyNotSetInConstructor */
final class BitBagShopwareInPostPlugin extends Plugin
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private EntityRepositoryInterface $shippingMethodRepository;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private CreateShippingMethodFactory $createShippingMethodFactory;

    public function setShippingMethodRepository(EntityRepositoryInterface $shippingMethodRepository): void
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function setCreateShippingMethodFactory(CreateShippingMethodFactory $createShippingMethodFactory): void
    {
        $this->createShippingMethodFactory = $createShippingMethodFactory;
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
        $shippingKey = ShippingMethodFactoryInterface::SHIPPING_KEY;

        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', $shippingKey));

        $shippingMethod = $this->shippingMethodRepository->searchIds($criteria, $context);
        if ($shippingMethod->getTotal()) {
            return;
        }

        $this->createShippingMethodFactory->create($shippingKey, $context);
    }

    private function activateShippingMethod(Context $context): void
    {
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodFactoryInterface::SHIPPING_KEY));

        $shippingMethod = $this->shippingMethodRepository->search($criteria, $context);
        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();
            if (true === $firstShippingMethod->getActive()) {
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
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodFactoryInterface::SHIPPING_KEY));

        $shippingMethod = $this->shippingMethodRepository->search($criteria, $context);
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
