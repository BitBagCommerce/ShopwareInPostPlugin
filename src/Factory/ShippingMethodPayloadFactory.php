<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
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
