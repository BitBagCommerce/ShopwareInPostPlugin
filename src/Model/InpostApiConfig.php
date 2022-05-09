<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Model;

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
