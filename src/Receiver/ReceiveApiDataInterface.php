<?php

declare(strict_types=1);

namespace BitBag\InPost\Receiver;

interface ReceiveApiDataInterface
{
    public function getData(): array;
}
