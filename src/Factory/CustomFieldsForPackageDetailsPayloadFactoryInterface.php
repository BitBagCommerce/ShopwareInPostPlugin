<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

interface CustomFieldsForPackageDetailsPayloadFactoryInterface
{
    public const PACKAGE_DETAILS_KEY = 'package_details';

    public function create(): array;
}
