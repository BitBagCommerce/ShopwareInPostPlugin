<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Finder\DeliveryTimeFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class ShippingMethodPayloadFactory implements ShippingMethodPayloadFactoryInterface
{
    private DeliveryTimeFinderInterface $deliveryTimeFinder;

    private RuleFinderInterface $ruleFinder;

    private DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory;

    private EntityRepositoryInterface $deliveryTimeRepository;

    public function __construct(
        DeliveryTimeFinderInterface $deliveryTimeFinder,
        RuleFinderInterface $ruleFinder,
        DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory,
        EntityRepositoryInterface $deliveryTimeRepository
    ) {
        $this->deliveryTimeFinder = $deliveryTimeFinder;
        $this->ruleFinder = $ruleFinder;
        $this->createDeliveryTimeFactory = $createDeliveryTimeFactory;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function create(string $name, Context $context): array
    {
        $ruleId = $this->ruleFinder->getRuleIdsByName('Cart >= 0', $context)->firstId();

        $inPostShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name,
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'availabilityRuleId' => $ruleId,
        ];

        $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();

        if (null === $deliveryId) {
            $this->deliveryTimeRepository->create([$this->createDeliveryTimeFactory->create()], $context);

            $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();
        }

        $inPostShippingMethod = array_merge($inPostShippingMethod, [
            'deliveryTimeId' => $deliveryId,
        ]);

        return $inPostShippingMethod;
    }
}
