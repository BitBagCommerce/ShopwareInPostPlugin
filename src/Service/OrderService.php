<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Service;

use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Struct\ArrayEntity;

final class OrderService implements OrderServiceInterface
{
    private EntityRepositoryInterface $orderDeliveryRepository;

    public function __construct(EntityRepositoryInterface $orderDeliveryRepository)
    {
        $this->orderDeliveryRepository = $orderDeliveryRepository;
    }

    public function saveTrackingNumberInOrder(
        array $shipments,
        array $orders,
        Context $context
    ): void {
        foreach ($shipments as $shipment) {
            $packageId = $shipment['id'];
            $trackingNumber = $shipment['tracking_number'];

            /** @var OrderEntity $order */
            foreach ($orders as $order) {
                $deliveries = $order->getDeliveries();

                if (null === $deliveries) {
                    continue;
                }

                $delivery = $deliveries->first();

                if (null === $delivery) {
                    continue;
                }

                $trackingCodes = $delivery->getTrackingCodes();

                /** @var ArrayEntity|null $inPostExtension */
                $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

                if (null === $inPostExtension) {
                    continue;
                }

                if (!in_array($trackingNumber, $trackingCodes) &&
                    $packageId === $inPostExtension->get('packageId')
                ) {
                    $this->orderDeliveryRepository->update([
                        [
                            'id' => $delivery->getId(),
                            'trackingCodes' => array_merge($trackingCodes, [$trackingNumber]),
                        ],
                    ], $context);
                }
            }
        }
    }
}
