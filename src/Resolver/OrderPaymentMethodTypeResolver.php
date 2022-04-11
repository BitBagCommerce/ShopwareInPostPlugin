<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\OrderException;
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
