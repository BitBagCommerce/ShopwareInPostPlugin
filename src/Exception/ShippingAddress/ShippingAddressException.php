<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception\ShippingAddress;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class ShippingAddressException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__SHIPPING_ADDRESS_DATA_FAILURE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
