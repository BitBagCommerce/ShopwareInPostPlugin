<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class PackageDetailsCustomFieldSetFinder implements PackageDetailsCustomFieldSetFinderInterface
{
    private EntityRepositoryInterface $customFieldSetRepository;

    public function __construct(EntityRepositoryInterface $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function search(Context $context): IdSearchResult
    {
        $customFieldsCriteria = (new Criteria())
            ->addFilter(
                new EqualsFilter(
                    CustomFieldsForPackageDetailsPayloadFactoryInterface::TECHNICAL_NAME,
                    CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY
                )
            );

        return $this->customFieldSetRepository->searchIds($customFieldsCriteria, $context);
    }
}
