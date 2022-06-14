<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

interface SalesChannelAwareWebClientInterface
{
    public const METHOD_POST = 'POST';

    public const SHIPMENTS_LABEL_URL = '%s/shipments/%s/label';

    public const SHIPMENTS_ORGANIZATIONS_URL = '%s/organizations/%s/shipments';

    public const ORGANIZATIONS_DISPATCH_ORDERS = '%s/organizations/%s/dispatch_orders';

    public function createShipment(array $data, string $salesChannelId): array;

    public function getLabelByShipmentId(int $shipmentId, ?string $salesChannelId = null): string;

    public function orderCourier(array $data, ?string $salesChannelId = null): array;
}
