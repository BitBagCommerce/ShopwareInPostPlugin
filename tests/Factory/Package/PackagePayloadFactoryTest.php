<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\Package\ParcelPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\ReceiverPayloadFactory;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolver;
use BitBag\ShopwareInPostPlugin\Resolver\OrderExtensionDataResolver;
use BitBag\ShopwareInPostPlugin\Resolver\OrderShippingAddressResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Uuid\Uuid;

final class PackagePayloadFactoryTest extends TestCase
{
    use CreateOrderTrait, CreateOrderShippingAddressTrait;

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

        $context = $this->createMock(Context::class);

        $orderShippingAddressResolver = $this->createMock(OrderShippingAddressResolverInterface::class);

        $orderShippingAddressResolver->expects(self::once())
                                     ->method('resolve')
                                     ->willReturn($orderShippingAddress);

        $createReceiverPayloadFactory = new ReceiverPayloadFactory($orderShippingAddressResolver);

        $parcelPayloadFactoryData = [
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
        ];

        $parcelPayloadFactory = $this->createMock(ParcelPayloadFactoryInterface::class);

        $parcelPayloadFactory->expects(self::once())
                                  ->method('create')
                                  ->willReturn($parcelPayloadFactoryData);

        $packagePayloadFactory = new PackagePayloadFactory(
            $createReceiverPayloadFactory,
            $parcelPayloadFactory,
            new OrderCustomFieldsResolver(),
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
            ],
            $packagePayloadFactory->create($order, $context)
        );
    }
}
