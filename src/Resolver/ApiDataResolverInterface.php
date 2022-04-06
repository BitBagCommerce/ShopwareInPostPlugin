<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

interface ApiDataResolverInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareInPostPlugin.config';

    public function getOrganizationId(): ?string;

    public function getAccessToken(): ?string;

    public function getEnvironment(): ?string;
}
