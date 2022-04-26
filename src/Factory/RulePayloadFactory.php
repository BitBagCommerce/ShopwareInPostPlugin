<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

final class RulePayloadFactory implements RulePayloadFactoryInterface
{
    public function create(string $name, string $paymentMethodId): array
    {
        return [
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
        ];
    }
}
