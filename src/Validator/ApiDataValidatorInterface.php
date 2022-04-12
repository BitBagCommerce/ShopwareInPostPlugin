<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use Shopware\Core\Framework\Context;

interface ApiDataValidatorInterface
{
    public function validate(Context $context): bool;
}
