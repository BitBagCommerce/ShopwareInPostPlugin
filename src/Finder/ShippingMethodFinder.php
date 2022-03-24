<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodFactoryInterface;
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
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodFactoryInterface::SHIPPING_KEY));

        return $this->shippingMethodRepository->search($criteria, $context);
    }

    public function searchIdsByShippingKey(Context $context): IdSearchResult
    {
        $criteria = (new Criteria())->addFilter(new ContainsFilter('name', ShippingMethodFactoryInterface::SHIPPING_KEY));

        return $this->shippingMethodRepository->searchIds($criteria, $context);
    }
}
