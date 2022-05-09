<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
