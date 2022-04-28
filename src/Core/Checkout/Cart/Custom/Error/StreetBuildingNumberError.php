<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error;

use Shopware\Core\Checkout\Cart\Error\Error;

final class StreetBuildingNumberError extends Error
{
    private string $addressId;

    public function __construct(string $addressId)
    {
        $this->addressId = $addressId;
        parent::__construct();
    }

    public function getId(): string
    {
        return $this->addressId;
    }

    public function getMessageKey(): string
    {
        return 'streetBuildingNumberInvalid';
    }

    public function getLevel(): int
    {
        return self::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return true;
    }

    public function getParameters(): array
    {
        return [];
    }
}
