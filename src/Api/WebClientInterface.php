<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
