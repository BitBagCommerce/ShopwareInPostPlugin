<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Framework\Uuid\Uuid;

final class DeliveryTimeFactory implements DeliveryTimeFactoryInterface
{
    public function create(): array
    {
        return [
            'id' => Uuid::randomHex(),
            'name' => '1-3 days',
            'min' => 1,
            'max' => 3,
            'unit' => 'day',
            'createdAt' => new \DateTime(),
        ];
    }
}
