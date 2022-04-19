<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory;

use BitBag\ShopwareInPostPlugin\Factory\DeliveryTimePayloadFactory;
use PHPUnit\Framework\TestCase;

final class DeliveryTimePayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new DeliveryTimePayloadFactory();

        self::assertEquals(
            [
                'name' => '1-3 days',
                'min' => 1,
                'max' => 3,
                'unit' => 'day',
            ],
            $factory->create()
        );
    }
}
