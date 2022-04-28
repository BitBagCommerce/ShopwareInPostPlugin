<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

interface RulePayloadFactoryInterface
{
    public const DISABLE_PAYMENT_CASH_ON_DELIVERY = 'Hide InPost when Cash on Delivery is chosen';

    public function create(string $name, string $paymentMethodId): array;
}
