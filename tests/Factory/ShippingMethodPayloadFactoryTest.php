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

final class ShippingMethodPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $deliveryTimeFinder = $this->createMock(DeliveryTimeFinderInterface::class);

        $ruleFinder = $this->createMock(RuleFinderInterface::class);

        $deliveryTimePayloadFactory = $this->createMock(DeliveryTimePayloadFactoryInterface::class);

        $deliveryTimeRepository = $this->createMock(EntityRepositoryInterface::class);

        $factory = new ShippingMethodPayloadFactory(
            $deliveryTimeFinder,
            $ruleFinder,
            $deliveryTimePayloadFactory,
            $deliveryTimeRepository
        );

        $context = Context::createDefaultContext();

        $ruleId = $ruleFinder->getRuleIdsByName('Cart >= 0', $context)->firstId();

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
