<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class OrderNotFoundException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__ORDER_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
