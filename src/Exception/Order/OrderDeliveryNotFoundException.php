<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
        return 'BITBAG_IN_POST_PLUGIN__ORDER_DELIVERY_NOT_FOUND_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
