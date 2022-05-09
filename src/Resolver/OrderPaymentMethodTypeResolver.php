<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderException;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;

final class OrderPaymentMethodTypeResolver implements OrderPaymentMethodTypeResolverInterface
{
    public function resolve(OrderEntity $order): string
    {
        $orderTransactions = $order->getTransactions();

        if (null === $orderTransactions) {
            throw new OrderException('order.paymentMethodNotFound');
        }

        /** @var OrderTransactionEntity|null $orderTransaction */
        $orderTransaction = $orderTransactions->first();

        if (null === $orderTransaction) {
            throw new OrderException('order.paymentMethodNotFound');
        }

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $orderTransaction->getPaymentMethod();

        return $paymentMethod->getHandlerIdentifier();
    }
}
