<?php

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
