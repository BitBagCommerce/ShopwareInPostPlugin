<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory;

use BitBag\ShopwareInPostPlugin\Factory\DeliveryTimePayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactory;
use BitBag\ShopwareInPostPlugin\Finder\DeliveryTimeFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\RuleFinderInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Uuid\Uuid;

final class ShippingMethodPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $deliveryTimeFinder = $this->createMock(DeliveryTimeFinderInterface::class);

        $ruleFinder = $this->createMock(RuleFinderInterface::class);

        $deliveryTimePayloadFactory = $this->createMock(DeliveryTimePayloadFactoryInterface::class);

        $deliveryTimeRepository = $this->createMock(EntityRepositoryInterface::class);

        $context = $this->createMock(Context::class);

        $factory = new ShippingMethodPayloadFactory(
            $deliveryTimeFinder,
            $ruleFinder,
            $deliveryTimePayloadFactory,
            $deliveryTimeRepository
        );

        $ruleFinder->expects($this->exactly(2))
                   ->method('getRuleIdsByName')
                   ->willReturn(
                       new IdSearchResult(
                           1,
                           ['data' => ['primaryKey' => Uuid::randomHex(), 'data' => ['id' => Uuid::randomHex()]]],
                           new Criteria(),
                           $context
                       )
                   );

        $ruleId = $ruleFinder->getRuleIdsByName('Cart >= 0', $context)->firstId();

        $deliveryTimeFinder->expects($this->exactly(2))
                           ->method('getDeliveryTimeIds')
                           ->willReturn(
                               new IdSearchResult(
                                   1,
                                   ['data' => ['primaryKey' => Uuid::randomHex(), 'data' => ['id' => Uuid::randomHex()]]],
                                   new Criteria(),
                                   $context
                               )
                           );

        $deliveryId = $deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();

        $shippingMethodName = 'shipping-method';

        self::assertEquals(
            [
                'name' => $shippingMethodName,
                'active' => true,
                'description' => $shippingMethodName,
                'taxType' => 'auto',
                'translated' => [
                    'name' => $shippingMethodName,
                ],
                'availabilityRuleId' => $ruleId,
                'deliveryTimeId' => $deliveryId,
            ],
            $factory->create($shippingMethodName, $context)
        );
    }
}
