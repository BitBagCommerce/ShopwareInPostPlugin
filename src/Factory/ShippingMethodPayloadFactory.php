<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Exception\RuleNotFoundException;
use BitBag\ShopwareInPostPlugin\Finder\DeliveryTimeFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class ShippingMethodPayloadFactory implements ShippingMethodPayloadFactoryInterface
{
    private EntityRepositoryInterface $shippingMethodRepository;

    private DeliveryTimeFinderInterface $deliveryTimeFinder;

    private RuleFinderInterface $ruleFinder;

    private DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory;

    private EntityRepositoryInterface $ruleRepository;

    private EntityRepositoryInterface $deliveryTimeRepository;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    public function __construct(
        EntityRepositoryInterface $shippingMethodRepository,
        DeliveryTimeFinderInterface $deliveryTimeFinder,
        RuleFinderInterface $ruleFinder,
        DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory,
        EntityRepositoryInterface $ruleRepository,
        EntityRepositoryInterface $deliveryTimeRepository,
        RulePayloadFactoryInterface $rulePayloadFactory
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->deliveryTimeFinder = $deliveryTimeFinder;
        $this->ruleFinder = $ruleFinder;
        $this->createDeliveryTimeFactory = $createDeliveryTimeFactory;
        $this->ruleRepository = $ruleRepository;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    public function create(string $name, Context $context): array
    {
        $ruleId = $this->getRuleId($context);

        $currencyId = $context->getCurrencyId();

        $inPostShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name,
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'availabilityRuleId' => $ruleId,
            'prices' => [
                [
                    'ruleId' => $ruleId,
                    'calculation' => 1,
                    'quantityStart' => 1,
                    'currencyPrice' => [
                        $currencyId => [
                            'net' => 0.0,
                            'gross' => 0.0,
                            'linked' => false,
                            'currencyId' => $currencyId,
                        ],
                    ],
                    'createdAt' => new \DateTime(),
                ],
            ],
            'createdAt' => new \DateTime(),
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

    private function getRuleId(Context $context): string
    {
        $ruleName = 'Cart >= 0';
        $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        if (0 === $rule->getTotal()) {
            $rule = $this->rulePayloadFactory->create($ruleName);

            $this->ruleRepository->create([$rule], $context);

            $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        }

        if (0 === $rule->getTotal()) {
            throw new RuleNotFoundException('rule.notFound');
        }

        $ruleId = $rule->firstId();

        if (null === $ruleId) {
            throw new RuleNotFoundException('rule.notFound');
        }

        return $ruleId;
    }
}
