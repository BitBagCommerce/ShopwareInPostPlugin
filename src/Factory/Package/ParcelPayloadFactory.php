<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Calculator\CentimetersToMillimetersCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class ParcelPayloadFactory implements ParcelPayloadFactoryInterface
{
    private const MAX_HEIGHT_AVAILABLE = 640;

    private const MAX_WIDTH_AVAILABLE = 380;

    private const MAX_DEPTH_AVAILABLE = 410;

    private const MAX_WEIGHT_AVAILABLE = 25;

    private OrderWeightCalculatorInterface $orderWeightCalculator;

    private OrderCustomFieldsResolverInterface $customFieldsResolver;

    private CentimetersToMillimetersCalculatorInterface $unitConverter;

    public function __construct(
        OrderWeightCalculatorInterface $orderWeightCalculator,
        OrderCustomFieldsResolverInterface $customFieldsResolver,
        CentimetersToMillimetersCalculatorInterface $unitConverter
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->customFieldsResolver = $customFieldsResolver;
        $this->unitConverter = $unitConverter;
    }

    public function create(OrderEntity $order, Context $context): array
    {
        $orderCustomFields = $this->customFieldsResolver->resolve($order);

        $weight = $this->orderWeightCalculator->calculate($order, $context);

        if (0.0 === $weight) {
            throw new PackageException('package.nullWeight');
        }

        if (self::MAX_WEIGHT_AVAILABLE < $weight) {
            throw new PackageException('package.tooHeavy');
        }

        $depth = $this->unitConverter->calculate($orderCustomFields['depth']);
        $width = $this->unitConverter->calculate($orderCustomFields['width']);
        $height = $this->unitConverter->calculate($orderCustomFields['height']);

        if (self::MAX_DEPTH_AVAILABLE < $depth ||
            self::MAX_WIDTH_AVAILABLE < $width ||
            self::MAX_HEIGHT_AVAILABLE < $height
        ) {
            throw new PackageException('package.tooLarge');
        }

        return [
            'dimensions' => [
                'length' => $depth,
                'width' => $width,
                'height' => $height,
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $weight,
                'unit' => 'kg',
            ],
        ];
    }
}
