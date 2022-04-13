<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use BitBag\ShopwareInPostPlugin\Resolver\OrderPaymentMethodTypeResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;

final class PackagePayloadFactory implements PackagePayloadFactoryInterface
{
    private ReceiverPayloadFactoryInterface $createReceiverPayloadFactory;

    private ParcelPayloadFactoryInterface $createParcelPayloadFactory;

    private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver;

    private OrderPaymentMethodTypeResolverInterface $orderPaymentMethodTypeResolver;

    public function __construct(
        ReceiverPayloadFactoryInterface $createReceiverPayloadFactory,
        ParcelPayloadFactoryInterface $parcelPayloadFactory,
        OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        OrderPaymentMethodTypeResolverInterface $orderPaymentMethodTypeResolver
    ) {
        $this->createReceiverPayloadFactory = $createReceiverPayloadFactory;
        $this->createParcelPayloadFactory = $parcelPayloadFactory;
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->orderPaymentMethodTypeResolver = $orderPaymentMethodTypeResolver;
    }

    public function create(OrderEntity $order): array
    {
        $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $inPostExtension) {
            throw new OrderException('order.extension.notFound');
        }

        $orderInPostExtensionData = $inPostExtension->getVars()['data'];

        if (!isset($orderInPostExtensionData['pointName'])) {
            throw new PackageException('package.pointNameNotFound');
        }

        $data = [
            'receiver' => $this->createReceiverPayloadFactory->create($order),
            'parcels' => [
                $this->createParcelPayloadFactory->create($order),
            ],
            'service' => WebClientInterface::INPOST_LOCKER_STANDARD_SERVICE,
            'custom_attributes' => [
                'target_point' => $orderInPostExtensionData['pointName'],
            ],
        ];

        $data = $this->addInsurance($data, $order);

        $data = $this->addCollectionAmount($data, $order);

        return $data;
    }

    private function addInsurance(array $data, OrderEntity $order): array
    {
        $customFieldInsurance = $this->orderCustomFieldsResolver->resolve($order)['insurance'];

        if (null !== $customFieldInsurance) {
            $data['insurance'] = [
                'amount' => $customFieldInsurance,
                'currency' => Defaults::CURRENCY,
            ];
        }

        return $data;
    }

    private function addCollectionAmount(array $data, OrderEntity $order): array
    {
        $paymentMethodType = $this->orderPaymentMethodTypeResolver->resolve($order);

        if ($paymentMethodType === CashPayment::class) {
            $data['cod'] = [
                'amount' => $order->getAmountTotal(),
                'currency' => Defaults::CURRENCY,
            ];
        }

        return $data;
    }
}
