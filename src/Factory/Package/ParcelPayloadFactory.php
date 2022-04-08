<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class ParcelPayloadFactory implements ParcelPayloadFactoryInterface
{
    private OrderWeightCalculatorInterface $orderWeightCalculator;

    private OrderCustomFieldsResolverInterface $customFieldsResolver;

    public function __construct(
        OrderWeightCalculatorInterface $orderWeightCalculator,
        OrderCustomFieldsResolverInterface $customFieldsResolver
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->customFieldsResolver = $customFieldsResolver;
    }

    public function create(OrderEntity $order): array
    {
        $orderCustomFields = $this->customFieldsResolver->getPackageDetails($order);

        $weight = $this->orderWeightCalculator->calculate($order);

        if (0.0 === $weight) {
            throw new PackageException('package.nullWeight');
        }

        return [
            'dimensions' => [
                'length' => $orderCustomFields['depth'],
                'width' => $orderCustomFields['width'],
                'height' => $orderCustomFields['height'],
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $weight,
                'unit' => 'kg',
            ],
        ];
    }
}
