<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

interface RulePayloadFactoryInterface
{
    public const DISABLE_PAYMENT_CASH_ON_DELIVERY = 'Hide InPost when Cash on Delivery is chosen';

    public function create(string $name, string $paymentMethodId): array;
}
