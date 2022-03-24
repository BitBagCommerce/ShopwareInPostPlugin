<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

interface ApiDataResolverInterface
{
    public function getData(): array;
}
