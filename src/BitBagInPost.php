<?php

declare(strict_types=1);

namespace BitBag\InPost;

use BitBag\InPost\Factory\CreateCustomFieldsForPackageDetailsInterface;
use BitBag\InPost\Factory\CreateShippingMethodFactory;
use BitBag\InPost\Factory\ShippingMethodFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;

final class BitBagInPost extends Plugin
{
    public function activate(ActivateContext $context): void
    {
        $this->createShippingMethod($context->getContext());
        $this->createCustomFieldsForPackageDetails($context->getContext());
    }

    private function createShippingMethod(Context $context): void
    {
        $shippingKey = ShippingMethodFactoryInterface::SHIPPING_KEY;

        /** @var EntityRepositoryInterface $shippingMethodRepository */
        $shippingMethodRepository = $this->container->get('shipping_method.repository');

        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', $shippingKey));

        $shippingMethod = $shippingMethodRepository->searchIds($criteria, $context);
        if ($shippingMethod->getTotal()) {
            return;
        }

        /** @var CreateShippingMethodFactory $createShippingMethodFactory */
        $createShippingMethodFactory = $this->container->get('bitbag.inpost.factory.create_shipping_method_factory');
        $createShippingMethodFactory->create($shippingKey, $context);
    }

    private function createCustomFieldsForPackageDetails(Context $context): void
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodFactoryInterface::SHIPPING_KEY.' package details'));

        $shippingMethod = $customFieldSetRepository->searchIds($criteria, $context);
        if ($shippingMethod->getTotal()) {
            return;
        }

        /** @var CreateCustomFieldsForPackageDetailsInterface $createCustomFieldsForPackageDetails */
        $createCustomFieldsForPackageDetails = $this->container->get('bitbag.inpost.factory.create_custom_fields_for_package_details');
        $createCustomFieldsForPackageDetails->create($context);
    }
}
