<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class DeliveryTimeFinder implements DeliveryTimeFinderInterface
{
    private EntityRepositoryInterface $deliveryTimeRepository;

    public function __construct(EntityRepositoryInterface $deliveryTimeRepository)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function getDeliveryTimeIds(Context $context): IdSearchResult
    {
        $deliveryTimeCriteria = (new Criteria())
            ->addFilter(new ContainsFilter('unit', 'day'))
            ->addFilter(new EqualsFilter('min', 1))
            ->addFilter(new EqualsFilter('max', 3));

        return $this->deliveryTimeRepository->searchIds($deliveryTimeCriteria, $context);
    }
}
