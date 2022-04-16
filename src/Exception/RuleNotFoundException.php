<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class RuleNotFoundException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_INPOST_PLUGIN__RULE_NOT_FOUND_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
