<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
