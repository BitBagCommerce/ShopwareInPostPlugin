<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class ShippingMethodFinder implements ShippingMethodFinderInterface
{
    private EntityRepositoryInterface $shippingMethodRepository;

    public function __construct(EntityRepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function searchByShippingKey(Context $context): EntitySearchResult
    {
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        return $this->shippingMethodRepository->search($criteria, $context);
    }

    public function searchIdsByShippingKey(Context $context): IdSearchResult
    {
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        return $this->shippingMethodRepository->searchIds($criteria, $context);
    }
}
