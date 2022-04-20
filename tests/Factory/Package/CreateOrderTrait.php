<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Uuid\Uuid;

trait CreateOrderTrait
{
    public function getOrder(OrderAddressEntity $orderShippingAddress, ArrayEntity $orderExtension): OrderEntity
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        $orderCustomer = new OrderCustomerEntity();
        $orderCustomer->setEmail('email@website.com');

        $orderDelivery = new OrderDeliveryEntity();
        $orderDelivery->setUniqueIdentifier(Uuid::randomHex());
        $orderDelivery->setShippingOrderAddress($orderShippingAddress);

        $paymentMethod = new PaymentMethodEntity();
        $paymentMethod->setName('payment-method');
        $paymentMethod->setHandlerIdentifier(CashPayment::class);

        $orderTransaction = new OrderTransactionEntity();
        $orderTransaction->setUniqueIdentifier(Uuid::randomHex());
        $orderTransaction->setPaymentMethod($paymentMethod);

        $order = new OrderEntity();
        $order->setId(Uuid::randomHex());
        $order->setOrderCustomer($orderCustomer);
        $order->setDeliveries(new OrderDeliveryCollection([$orderDelivery]));
        $order->setExtensions([OrderInPostExtensionInterface::PROPERTY_KEY => $orderExtension]);
        $order->setCustomFields([
            "${packageDetailsKey}_depth" => 20,
            "${packageDetailsKey}_width" => 30,
            "${packageDetailsKey}_height" => 45,
        ]);
        $order->setTransactions(new OrderTransactionCollection([$orderTransaction]));
        $order->setAmountTotal(50);

        return $order;
    }
}
