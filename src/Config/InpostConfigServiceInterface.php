<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Config;

interface InpostConfigServiceInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareInPostPlugin.config';

    public function getInpostApiConfig(): InpostApiConfig;
}
