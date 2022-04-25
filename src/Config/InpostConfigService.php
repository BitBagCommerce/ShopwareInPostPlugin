<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Config;

use BitBag\ShopwareInPostPlugin\Exception\ApiDataException;
use BitBag\ShopwareInPostPlugin\Model\InpostApiConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class InpostConfigService implements InpostConfigServiceInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getInpostApiConfig(): InpostApiConfig
    {
        $organizationId = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId') ?: null;
        $accessToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken') ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment') ?: null;

        if (null === $organizationId || null === $accessToken || null === $environment) {
            throw new ApiDataException('api.credentialsDataNotFound');
        }

        return new InpostApiConfig($organizationId, $accessToken, $environment);
    }
}
