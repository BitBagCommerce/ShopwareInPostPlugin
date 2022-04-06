<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use Shopware\Core\System\SystemConfig\SystemConfigService;

final class ApiDataResolver implements ApiDataResolverInterface
{
    private const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareInPostPlugin.config';

    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getOrganizationId(): ?string
    {
        return $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId') ?: null;
    }

    public function getAccessToken(): ?string
    {
        return $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken') ?: null;
    }

    public function getEnvironment(): ?string
    {
        return $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment') ?: null;
    }
}
