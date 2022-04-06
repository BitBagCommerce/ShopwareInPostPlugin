<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Filter;

use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

final class CustomFieldSetForPackageDetailsFilter extends EqualsFilter
{
    private const TECHNICAL_NAME = 'config.technical_name';

    public function __construct()
    {
        parent::__construct(self::TECHNICAL_NAME, CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY);
    }
}
