<?php

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