<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

final class DeliveryTimePayloadFactory implements DeliveryTimePayloadFactoryInterface
{
    public function create(): array
    {
        return [
            'name' => '1-3 days',
            'min' => 1,
            'max' => 3,
            'unit' => 'day',
        ];
    }
}
