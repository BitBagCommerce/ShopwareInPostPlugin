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

    public function getData(): array
    {
        return [
            'organizationId' => $this->systemConfigService->get(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId'),
            'accessToken' => $this->systemConfigService->get(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken'),
            'environment' => $this->systemConfigService->get(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment'),
        ];
    }
}
