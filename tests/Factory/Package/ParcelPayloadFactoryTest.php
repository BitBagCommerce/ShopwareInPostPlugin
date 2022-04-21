<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\ParcelPayloadFactory;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderEntity;

final class ParcelPayloadFactoryTest extends TestCase
{
    public const ORDER_WEIGHT = 1.8;

    public const INPOST_POINT_NAME = 'POP-WAW405';

    public const PACKAGE_DEPTH = 20;

    public const PACKAGE_WIDTH = 30;

    public const PACKAGE_HEIGHT = 45;

    public function testCreate(): void
    {
        $orderCustomFieldsValues = [
            'depth' => self::PACKAGE_DEPTH,
            'width' => self::PACKAGE_WIDTH,
            'height' => self::PACKAGE_HEIGHT,
        ];

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);

        $orderWeightCalculator->expects(self::once())
                              ->method('calculate')
                              ->willReturn(self::ORDER_WEIGHT);

        $orderCustomFieldsResolver = $this->createMock(OrderCustomFieldsResolverInterface::class);

        $orderCustomFieldsResolver->expects(self::once())
                                  ->method('resolve')
                                  ->willReturn($orderCustomFieldsValues);

        $parcelPayloadFactory = new ParcelPayloadFactory(
            $orderWeightCalculator,
            $orderCustomFieldsResolver
        );

        self::assertEquals(
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
            $parcelPayloadFactory->create($this->getOrder())
        );
    }

    private function getOrder(): OrderEntity
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        $order = new OrderEntity();
        $order->setCustomFields([
            "${packageDetailsKey}_depth" => self::PACKAGE_DEPTH,
            "${packageDetailsKey}_width" => self::PACKAGE_WIDTH,
            "${packageDetailsKey}_height" => self::PACKAGE_HEIGHT,
        ]);

        return $order;
    }
}
