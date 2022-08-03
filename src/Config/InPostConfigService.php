<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Config;

use BitBag\ShopwareInPostPlugin\Exception\ApiDataException;
use BitBag\ShopwareInPostPlugin\Model\InPostApiConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class InPostConfigService implements InPostConfigServiceInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getInPostApiConfig(?string $salesChannelId = null): InPostApiConfig
    {
        $organizationId = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId', $salesChannelId) ?: null;
        $accessToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken', $salesChannelId) ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment', $salesChannelId) ?: null;
        $widgetToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostWidgetToken', $salesChannelId) ?: null;

        if (null === $organizationId || null === $accessToken || null === $environment || null === $widgetToken) {
            throw new ApiDataException('api.credentialsDataNotFound');
        }

        return new InPostApiConfig(
            $organizationId,
            $accessToken,
            $environment,
            $widgetToken
        );
    }
}
