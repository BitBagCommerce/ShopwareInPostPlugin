<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Calculator\CentimetersToMillimetersCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class ParcelPayloadFactory implements ParcelPayloadFactoryInterface
{
    private OrderWeightCalculatorInterface $orderWeightCalculator;

    private OrderCustomFieldsResolverInterface $customFieldsResolver;

    private CentimetersToMillimetersCalculatorInterface $centimetersToMillimetersCalculator;

    public function __construct(
        OrderWeightCalculatorInterface $orderWeightCalculator,
        OrderCustomFieldsResolverInterface $customFieldsResolver,
        CentimetersToMillimetersCalculatorInterface $centimetersToMillimetersCalculator
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->customFieldsResolver = $customFieldsResolver;
        $this->centimetersToMillimetersCalculator = $centimetersToMillimetersCalculator;
    }

    public function create(OrderEntity $order): array
    {
        $orderCustomFields = $this->customFieldsResolver->resolve($order);

        $weight = $this->orderWeightCalculator->calculate($order);

        if (0.0 === $weight) {
            throw new PackageException('package.nullWeight');
        }

        return [
            'dimensions' => [
                'length' => $this->centimetersToMillimetersCalculator->calculate($orderCustomFields['depth']),
                'width' => $this->centimetersToMillimetersCalculator->calculate($orderCustomFields['width']),
                'height' => $this->centimetersToMillimetersCalculator->calculate($orderCustomFields['height']),
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $weight,
                'unit' => 'kg',
            ],
        ];
    }
}
