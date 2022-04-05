<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

interface ApiDataResolverInterface
{
    public function getOrganizationId(): ?string;

    public function getAccessToken(): ?string;

    public function getEnvironment(): ?string;
}
