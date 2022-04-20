<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory;

use BitBag\ShopwareInPostPlugin\Factory\RulePayloadFactory;
use PHPUnit\Framework\TestCase;

final class RulePayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'rule-factory';

        self::assertEquals(
            [
                'name' => $name,
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'cartCartAmount',
                        'value' => [
                            'amount' => 0,
                            'operator' => '>=',
                        ],
                    ],
                ],
            ],
            (new RulePayloadFactory())->create($name)
        );
    }
}
