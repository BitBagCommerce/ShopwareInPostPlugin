<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Resolver;

use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderCustomFieldsResolver implements OrderCustomFieldsResolverInterface
{
    public function getPackageDetails(OrderEntity $order): array
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        /**
         * @psalm-return array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields();

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $insuranceKey = $packageDetailsKey . '_insurance';

        if (!isset($orderCustomFields[$depthKey], $orderCustomFields[$heightKey], $orderCustomFields[$widthKey])) {
            throw new PackageException('package.fillRequiredCustomFields');
        }

        return [
            'depth' => $orderCustomFields[$depthKey],
            'height' => $orderCustomFields[$heightKey],
            'width' => $orderCustomFields[$widthKey],
            'insurance' => $orderCustomFields[$insuranceKey] ?? null,
        ];
    }
}
