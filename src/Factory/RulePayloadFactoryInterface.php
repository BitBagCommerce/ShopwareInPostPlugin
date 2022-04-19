<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

interface RulePayloadFactoryInterface
{
    public function create(string $name): array;
}
