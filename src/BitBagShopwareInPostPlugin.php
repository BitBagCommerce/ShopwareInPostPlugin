<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

final class BitBagShopwareInPostPlugin extends Plugin
{
    private EntityRepositoryInterface $shippingMethodRepository;

    private ShippingMethodPayloadFactoryInterface $shippingMethodFactory;

    private ShippingMethodFinderInterface $shippingMethodFinder;

    private EntityRepositoryInterface $customFieldSetRepository;

    private CustomFieldsForPackageDetailsPayloadFactory $customFieldsForPackageDetailsPayloadFactory;

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

    public function setCustomFieldSetRepository(EntityRepositoryInterface $customFieldSetRepository): void
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function setCustomFieldsForPackageDetailsPayloadFactory(
        CustomFieldsForPackageDetailsPayloadFactory $customFieldsForPackageDetailsPayloadFactory
    ): void {
        $this->customFieldsForPackageDetailsPayloadFactory = $customFieldsForPackageDetailsPayloadFactory;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->createShippingMethod($activateContext->getContext());
        $this->toggleActiveShippingMethod(true, $activateContext->getContext());
        $this->createCustomFieldSetForPackageDetails($activateContext->getContext());
        $this->setActiveCustomFieldsSetForPackageDetails(true, $activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->toggleActiveShippingMethod(false, $deactivateContext->getContext());
        $this->setActiveCustomFieldsSetForPackageDetails(false, $deactivateContext->getContext());
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

    private function createCustomFieldSetForPackageDetails(Context $context): void
    {
        $customFieldsCriteria = (new Criteria())->addFilter(new EqualsFilter('name', 'Package details'));
        /** @var IdSearchResult $customFields */
        $customFields = $this->customFieldSetRepository->searchIds($customFieldsCriteria, $context);
        if ($customFields->getTotal()) {
            return;
        }

        $data = $this->customFieldsForPackageDetailsPayloadFactory->create();

        $this->customFieldSetRepository->upsert([$data], $context);
    }

    private function setActiveCustomFieldsSetForPackageDetails(bool $active, Context $context): void
    {
        $customFieldsCriteria = (new Criteria())->addFilter(new EqualsFilter('name', 'Package details'));
        /** @var IdSearchResult $customFields */
        $customFields = $this->customFieldSetRepository->searchIds($customFieldsCriteria, $context);
        if (!$customFields->getTotal()) {
            return;
        }

        $this->customFieldSetRepository->update([
            [
                'id' => $customFields->firstId(),
                'active' => $active,
            ],
        ], $context);
    }
}
