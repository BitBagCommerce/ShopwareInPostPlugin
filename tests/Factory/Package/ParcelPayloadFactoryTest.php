<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Calculator\CentimetersToMillimetersCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\ParcelPayloadFactory;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class ParcelPayloadFactoryTest extends TestCase
{
    public const ORDER_WEIGHT = 1.8;

    public const INPOST_POINT_NAME = 'POP-WAW405';

    public const PACKAGE_DEPTH = 20;

    public const PACKAGE_WIDTH = 30;

    public const PACKAGE_HEIGHT = 45.5;

    public function testCreate(): void
    {
        $orderCustomFieldsValues = [
            'depth' => self::PACKAGE_DEPTH,
            'width' => self::PACKAGE_WIDTH,
            'height' => self::PACKAGE_HEIGHT,
        ];

        $context = $this->createMock(Context::class);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);

        $orderWeightCalculator->expects(self::once())
                              ->method('calculate')
                              ->willReturn(self::ORDER_WEIGHT);

        $orderCustomFieldsResolver = $this->createMock(OrderCustomFieldsResolverInterface::class);

        $orderCustomFieldsResolver->expects(self::once())
                                  ->method('resolve')
                                  ->willReturn($orderCustomFieldsValues);

        $centimetersToMillimetersCalculator = $this->createMock(CentimetersToMillimetersCalculatorInterface::class);

        $centimetersToMillimetersCalculator->expects(self::exactly(3))
                                           ->method('calculate')
                                           ->withConsecutive([20], [30], [45.5])
                                           ->willReturnOnConsecutiveCalls(200, 300, 455);

        $parcelPayloadFactory = new ParcelPayloadFactory(
            $orderWeightCalculator,
            $orderCustomFieldsResolver,
            $centimetersToMillimetersCalculator
        );

        self::assertEquals(
            [
                'dimensions' => [
                    'length' => 200,
                    'width' => 300,
                    'height' => 455,
                    'unit' => 'mm',
                ],
                'weight' => [
                    'amount' => self::ORDER_WEIGHT,
                    'unit' => 'kg',
                ],
            ],
            $parcelPayloadFactory->create($this->getOrder(), $context)
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
