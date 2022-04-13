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

    public const CONFIRMED_STATUS = 'confirmed';

    public const INPOST_LOCKER_STANDARD_SERVICE = 'inpost_locker_standard';

    public const INPOST_LOCKER_PASS_THRU_SERVICE = 'inpost_locker_pass_thru';

    public const SMS_ADDITIONAL_SERVICE = 'sms';

    public const EMAIL_ADDITIONAL_SERVICE = 'email';

    public const SATURDAY_ADDITIONAL_SERVICE = 'saturday';

    public const ROD_ADDITIONAL_SERVICE = 'rod';

    public const STATUS_CREATED = 'created';

    public function getPointByName(string $name, int $attempts = 0): ?array;

    public function getOrganizations(): array;

    public function getShipmentById(int $id): ?array;

    public function getLabels(array $shipmentIds): ?string;

    public function getShipments(): ?array;

    public function createShipment(array $data): array;

    public function request(string $method, string $url, array $data = []): string;

    public function getLabelByShipmentId(int $shipmentId): string;
}
