<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;

final class CreatePackagePayloadFactory implements CreatePackagePayloadFactoryInterface
{
    private CreateReceiverPackagePayloadFactoryInterface $createReceiverPackagePayloadFactory;

    private CreateParcelPackagePayloadFactoryInterface $createParcelPackagePayloadFactory;

    public function __construct(
        CreateReceiverPackagePayloadFactoryInterface $createReceiverPackagePayloadFactory,
        CreateParcelPackagePayloadFactoryInterface $createParcelPackagePayloadFactory
    ) {
        $this->createReceiverPackagePayloadFactory = $createReceiverPackagePayloadFactory;
        $this->createParcelPackagePayloadFactory = $createParcelPackagePayloadFactory;
    }

    public function create(OrderEntity $order): array
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        $orderCustomFields = $order->getCustomFields();

        if (null === $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY)) {
            throw new \Exception('order.extension.notFoundInPost');
        }

        $orderInPostExtensionData = $orderExtension->getVars()['data'];

        $data = [
            'receiver' => $this->createReceiverPackagePayloadFactory->create($order),
            'parcels' => [
                $this->createParcelPackagePayloadFactory->create($order),
            ],
            'service' => WebClientInterface::INPOST_LOCKER_STANDARD_SERVICE,
            'custom_attributes' => [
                'target_point' => $orderInPostExtensionData['pointName'],
            ],
        ];

        $customFieldInsurance = $packageDetailsKey . '_insurance';
        if (isset($orderCustomFields[$customFieldInsurance]) && $orderCustomFields[$packageDetailsKey . '_insurance']) {
            $data['insurance'] = [
                'amount' => $orderCustomFields[$packageDetailsKey . '_insurance'],
                'currency' => Defaults::CURRENCY,
            ];
        }

        if (null === $orderTransactions = $order->getTransactions()) {
            throw new \Exception('error.wentWrong');
        }

        /** @var OrderTransactionEntity $orderTransaction */
        $orderTransaction = $orderTransactions->first();

        /** @var PaymentMethodEntity $paymentMethod */
        $paymentMethod = $orderTransaction->getPaymentMethod();

        $handlerIdentifier = $paymentMethod->getHandlerIdentifier();
        if ($handlerIdentifier === CashPayment::class) {
            $data['cod'] = [
                'amount' => $order->getAmountTotal(),
                'currency' => Defaults::CURRENCY,
            ];
        }

        return $data;
    }
}
