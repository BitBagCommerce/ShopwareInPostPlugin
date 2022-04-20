<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\Package\ParcelPayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\Package\ReceiverPayloadFactory;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolver;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use BitBag\ShopwareInPostPlugin\Resolver\OrderExtensionDataResolver;
use BitBag\ShopwareInPostPlugin\Resolver\OrderPaymentMethodTypeResolver;
use BitBag\ShopwareInPostPlugin\Resolver\OrderShippingAddressResolverInterface;
use PHPUnit\Framework\TestCase;
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

final class PackagePayloadFactoryTest extends TestCase
{
    public const ORDER_WEIGHT = 1.8;

    public const INPOST_POINT_NAME = 'POP-WAW405';

    public const PACKAGE_DEPTH = 20;

    public const PACKAGE_WIDTH = 30;

    public const PACKAGE_HEIGHT = 45;

    public function testCreate(): void
    {
        $orderShippingAddress = $this->getOrderShippingAddress();

        $orderCustomFieldsValues = [
            'depth' => self::PACKAGE_DEPTH,
            'width' => self::PACKAGE_WIDTH,
            'height' => self::PACKAGE_HEIGHT,
        ];

        $inPostOrderExtensionId = Uuid::randomHex();

        $inPostOrderExtensionData = [
            'id' => $inPostOrderExtensionId,
            'pointName' => self::INPOST_POINT_NAME,
            'packageId' => null,
        ];

        $orderExtension = new ArrayEntity($inPostOrderExtensionData);
        $orderExtension->setUniqueIdentifier($inPostOrderExtensionId);

        $order = $this->getOrder($orderShippingAddress, $orderExtension);

        $orderShippingAddressResolver = $this->createMock(OrderShippingAddressResolverInterface::class);

        $orderShippingAddressResolver->expects(self::once())
                                     ->method('resolve')
                                     ->willReturn($orderShippingAddress);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);

        $orderWeightCalculator->expects(self::once())
                              ->method('calculate')
                              ->willReturn(self::ORDER_WEIGHT);

        $orderCustomFieldsResolver = $this->createMock(OrderCustomFieldsResolverInterface::class);

        $orderCustomFieldsResolver->expects(self::once())
                                  ->method('resolve')
                                  ->willReturn($orderCustomFieldsValues);

        $createReceiverPayloadFactory = new ReceiverPayloadFactory($orderShippingAddressResolver);

        $parcelPayloadFactory = new ParcelPayloadFactory(
            $orderWeightCalculator,
            $orderCustomFieldsResolver
        );

        $packagePayloadFactory = new PackagePayloadFactory(
            $createReceiverPayloadFactory,
            $parcelPayloadFactory,
            new OrderCustomFieldsResolver(),
            new OrderPaymentMethodTypeResolver(),
            new OrderExtensionDataResolver()
        );

        self::assertEquals(
            [
                'receiver' => [
                    'company_name' => null,
                    'first_name' => $orderShippingAddress->getFirstName(),
                    'last_name' => $orderShippingAddress->getLastName(),
                    'email' => 'email@website.com',
                    'phone' => '123456789',
                    'address' => [
                        'street' => 'Polna',
                        'building_number' => '11',
                        'city' => $orderShippingAddress->getCity(),
                        'post_code' => $orderShippingAddress->getZipcode(),
                        'country_code' => Defaults::CURRENCY_CODE,
                    ],
                ],
                'parcels' => [
                    [
                        'dimensions' => [
                            'length' => $orderCustomFieldsValues['depth'],
                            'width' => $orderCustomFieldsValues['width'],
                            'height' => $orderCustomFieldsValues['height'],
                            'unit' => 'mm',
                        ],
                        'weight' => [
                            'amount' => self::ORDER_WEIGHT,
                            'unit' => 'kg',
                        ],
                    ],
                ],
                'service' => WebClientInterface::INPOST_LOCKER_STANDARD_SERVICE,
                'custom_attributes' => [
                    'target_point' => $inPostOrderExtensionData['pointName'],
                ],
                'cod' => [
                    'amount' => $order->getAmountTotal(),
                    'currency' => Defaults::CURRENCY,
                ],
            ],
            $packagePayloadFactory->create($order)
        );
    }

    private function getOrder(OrderAddressEntity $orderShippingAddress, ArrayEntity $orderExtension): OrderEntity
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

    private function getOrderShippingAddress(): OrderAddressEntity
    {
        $orderShippingAddress = new OrderAddressEntity();
        $orderShippingAddress->setPhoneNumber('123-456-789');
        $orderShippingAddress->setStreet('Polna 11');
        $orderShippingAddress->setFirstName('Jan');
        $orderShippingAddress->setLastName('Kowalski');
        $orderShippingAddress->setCity('Warszawa');
        $orderShippingAddress->setZipcode('00-001');

        return $orderShippingAddress;
    }
}
