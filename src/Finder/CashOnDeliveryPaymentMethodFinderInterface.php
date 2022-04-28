<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

interface CashOnDeliveryPaymentMethodFinderInterface
{
    public function find(Context $context): IdSearchResult;
}
