<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

interface WebClientInterface
{
    public const PRODUCTION_API_ENDPOINT = 'https://api-shipx-pl.easypack24.net';

    public const SANDBOX_API_ENDPOINT = 'https://sandbox-api-shipx-pl.easypack24.net';

    public const SANDBOX_ENVIRONMENT = 'sandbox';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public const API_VERSION = 'v1';

    public const INPOST_LOCKER_STANDARD_SERVICE = 'inpost_locker_standard';

    public function createShipment(array $data): array;

    public function request(string $method, string $url, array $data = []): string;

    public function getLabelByShipmentId(int $shipmentId): string;
}
