<?php

declare(strict_types=1);

namespace BitBag\InPost\Factory;

use BitBag\InPost\Finder\ShippingParametersFinder;
use DateTime;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class CreateShippingMethodFactory implements CreateShippingMethodFactoryInterface
{
    private EntityRepositoryInterface $shippingMethodRepository;

    private ShippingParametersFinder $shippingParametersFinder;

    public function __construct(
        EntityRepositoryInterface $shippingMethodRepository,
        ShippingParametersFinder $shippingParametersFinder
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingParametersFinder = $shippingParametersFinder;
    }

    public function create(string $name, Context $context): void
    {
        $currentDateTime = new DateTime('now');

        $inPostShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name . " shipping method",
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'availabilityRuleId' => $this->shippingParametersFinder->getRuleIds($context)->firstId(),
            'createdAt' => $currentDateTime,
        ];

        $deliveryId = $this->shippingParametersFinder->getDeliveryTimeIds($context)->firstId();
        if ($deliveryId) {
            $inPostShippingMethod = array_merge($inPostShippingMethod, [
                'deliveryTimeId' => $deliveryId,
            ]);
        } else {
            $inPostShippingMethod = array_merge($inPostShippingMethod, [
                'deliveryTime' => [
                    'name' => '1-3 days',
                    'min' => 1,
                    'max' => 3,
                    'unit' => 'day',
                    'createdAt' => $currentDateTime,
                ],
            ]);
        }

        $this->shippingMethodRepository->create([$inPostShippingMethod], $context);
    }
}
