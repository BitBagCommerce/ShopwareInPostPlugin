<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

interface ShippingMethodFinderInterface
{
    public function searchByShippingKey(Context $context): EntitySearchResult;

    public function searchIdsByShippingKey(Context $context): IdSearchResult;
}
