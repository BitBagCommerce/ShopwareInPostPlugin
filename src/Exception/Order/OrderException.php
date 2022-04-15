<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception\Order;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class OrderException extends ShopwareHttpException
{
    public function __construct(string $message, string $orderId, array $parameters = [], ?\Throwable $e = null)
    {
        $message = sprintf($message, $orderId);

        parent::__construct($message, $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__ORDER_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
