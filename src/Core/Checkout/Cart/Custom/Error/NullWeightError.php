<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error;

use Shopware\Core\Checkout\Cart\Error\Error;

final class NullWeightError extends Error
{
    private string $cartToken;

    public function __construct(string $cartToken)
    {
        $this->cartToken = $cartToken;

        parent::__construct();
    }

    public function getId(): string
    {
        return $this->cartToken;
    }

    public function getMessageKey(): string
    {
        return 'nullWeightError';
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
