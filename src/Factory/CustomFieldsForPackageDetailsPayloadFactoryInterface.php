<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

interface CustomFieldsForPackageDetailsPayloadFactoryInterface
{
    public const PACKAGE_DETAILS_KEY = 'bitbag_inpost_point_package_details';

    public const TECHNICAL_NAME = 'config.technical_name';

    public function create(): array;
}
