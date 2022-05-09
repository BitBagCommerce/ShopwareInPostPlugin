<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Filter\CustomFieldSetForPackageDetailsFilter;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
        $customFieldsCriteria = (new Criteria())->addFilter(new CustomFieldSetForPackageDetailsFilter());

        return $this->customFieldSetRepository->searchIds($customFieldsCriteria, $context);
    }
}
