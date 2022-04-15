<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception\Order;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class OrderExtensionNotFoundException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__ORDER_EXTENSION_NOT_FOUND_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
