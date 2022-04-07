<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class CreateParcelPackagePayloadFactory implements CreateParcelPackagePayloadFactoryInterface
{
    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(OrderWeightCalculatorInterface $orderWeightCalculator)
    {
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function create(OrderEntity $order): array
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        /**
         * @psalm-return array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields();

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';

        if (!isset($orderCustomFields[$depthKey], $orderCustomFields[$heightKey], $orderCustomFields[$widthKey])) {
            throw new \Exception('package.fillRequiredCustomFields');
        }

        if (null === $orderLineItems = $order->getLineItems()) {
            throw new \Exception('error.wentWrong');
        }

        $weight = $this->orderWeightCalculator->calculate($orderLineItems);

        return [
            'dimensions' => [
                'length' => $orderCustomFields[$depthKey],
                'width' => $orderCustomFields[$heightKey],
                'height' => $orderCustomFields[$widthKey],
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $weight,
                'unit' => 'kg',
            ],
        ];
    }
}
