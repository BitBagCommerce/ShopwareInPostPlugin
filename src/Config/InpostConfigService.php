<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Config;

use BitBag\ShopwareInPostPlugin\Exception\ApiDataException;
use BitBag\ShopwareInPostPlugin\Model\InpostApiConfig;
use Shopware\Core\System\SystemConfig\Exception\InvalidSettingValueException;
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
        try {
            $organizationId = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId');
            $accessToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken');
            $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment');

            return new InpostApiConfig($organizationId, $accessToken, $environment);
        } catch (InvalidSettingValueException $e) {
            throw new ApiDataException('api.credentialsDataNotFound');
        }
    }
}
