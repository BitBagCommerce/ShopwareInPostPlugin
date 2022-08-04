<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\PackageDetailsCustomFieldSetFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

final class CustomFieldSetConfigurator implements CustomFieldSetConfiguratorInterface
{
    private PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder;

    private CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory;

    private EntityRepository $customFieldSetRepository;

    public function __construct(
        PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder,
        CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory,
        EntityRepository $customFieldSetRepository
    ) {
        $this->packageDetailsCustomFieldSetFinder = $packageDetailsCustomFieldSetFinder;
        $this->customFieldsForPackageDetailsPayloadFactory = $customFieldsForPackageDetailsPayloadFactory;
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function createCustomFieldSetForPackageDetails(Context $context): void
    {
        $customFields = $this->packageDetailsCustomFieldSetFinder->search($context);

        if (0 !== $customFields->getTotal()) {
            return;
        }

        $data = $this->customFieldsForPackageDetailsPayloadFactory->create();

        $this->customFieldSetRepository->upsert([$data], $context);
    }
}
