<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
