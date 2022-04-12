<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Config;

final class InpostApiConfig
{
    private string $organizationId;

    private string $accessToken;

    private string $environment;

    public function __construct(string $organizationId, string $accessToken, string $environment)
    {
        $this->organizationId = $organizationId;
        $this->accessToken = $accessToken;
        $this->environment = $environment;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
