<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class DeliveryTimeFinder implements DeliveryTimeFinderInterface
{
    private EntityRepository $deliveryTimeRepository;

    public function __construct(EntityRepository $deliveryTimeRepository)
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
