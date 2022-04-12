<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use Shopware\Core\Checkout\Order\OrderEntity;

interface InpostShippingMethodValidatorInterface
{
    public function validate(OrderEntity $order): bool;
}
