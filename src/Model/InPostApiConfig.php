<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Model;

final class InPostApiConfig
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
