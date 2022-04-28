<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory;

use BitBag\ShopwareInPostPlugin\Factory\RulePayloadFactory;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Uuid\Uuid;

final class RulePayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'rule-factory';

        $paymentMethodId = Uuid::randomHex();

        self::assertEquals(
            [
                'name' => $name,
                'priority' => 0,
                'conditions' => [
                    [
                        'type' => 'paymentMethod',
                        'value' => [
                            'paymentMethodIds' => [$paymentMethodId],
                            'operator' => '!=',
                        ],
                    ],
                ],
            ],
            (new RulePayloadFactory())->create($name, $paymentMethodId)
        );
    }
}
