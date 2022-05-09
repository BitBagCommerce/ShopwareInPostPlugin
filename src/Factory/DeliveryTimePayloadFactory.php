<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
