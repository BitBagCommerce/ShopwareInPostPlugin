<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

final class RulePayloadFactory implements RulePayloadFactoryInterface
{
    public function create(string $name): array
    {
        return [
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
            'createdAt' => new \DateTime(),
        ];
    }
}
