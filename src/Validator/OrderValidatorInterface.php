<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface OrderValidatorInterface
{
    public function validate(OrderEntity $order, Context $context): bool;
}
