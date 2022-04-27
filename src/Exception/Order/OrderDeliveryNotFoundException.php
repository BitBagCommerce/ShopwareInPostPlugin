<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception\Order;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class OrderDeliveryNotFoundException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct('order.shippingAddressNotFound');
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__ORDER_DELIVERY_NOT_FOUND_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
