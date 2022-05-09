<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Finder\DeliveryTimeFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class ShippingMethodPayloadFactory implements ShippingMethodPayloadFactoryInterface
{
    private DeliveryTimeFinderInterface $deliveryTimeFinder;

    private DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory;

    private EntityRepositoryInterface $deliveryTimeRepository;

    public function __construct(
        DeliveryTimeFinderInterface $deliveryTimeFinder,
        DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory,
        EntityRepositoryInterface $deliveryTimeRepository
    ) {
        $this->deliveryTimeFinder = $deliveryTimeFinder;
        $this->createDeliveryTimeFactory = $createDeliveryTimeFactory;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function create(string $name, string $ruleId, Context $context): array
    {
        $currencyId = $context->getCurrencyId();

        $inPostShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name,
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'customFields' => [
                'technical_name' => $name,
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
                ],
            ],
        ];

        $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();

        if (null === $deliveryId) {
            $this->deliveryTimeRepository->create([$this->createDeliveryTimeFactory->create()], $context);

            $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();
        }

        $inPostShippingMethod['deliveryTimeId'] = $deliveryId;

        return $inPostShippingMethod;
    }
}
