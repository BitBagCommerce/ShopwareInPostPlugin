<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
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
