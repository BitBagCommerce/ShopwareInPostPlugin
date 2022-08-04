<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderNotFoundException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

final class OrderFinder implements OrderFinderInterface
{
    private EntityRepository $orderRepository;

    public function __construct(EntityRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getWithAssociations(string $orderId, Context $context): OrderEntity
    {
        $orderCriteria = new Criteria([$orderId]);
        $orderCriteria->addAssociations([
            'deliveries',
            'lineItems',
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
            'transactions',
            'transactions.paymentMethod',
            OrderInPostExtensionInterface::PROPERTY_KEY,
            'salesChannel',
        ]);

        $order = $this->orderRepository->search($orderCriteria, $context)->first();

        if (null === $order) {
            throw new OrderNotFoundException('order.notFound');
        }

        return $order;
    }

    public function getWithAssociationsByOrdersIds(array $ordersIds, Context $context): EntitySearchResult
    {
        $orderCriteria = new Criteria($ordersIds);
        $orderCriteria->addAssociations([
            'deliveries',
            'lineItems',
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
            'transactions',
            'transactions.paymentMethod',
            OrderInPostExtensionInterface::PROPERTY_KEY,
            'salesChannel',
        ]);

        return $this->orderRepository->search($orderCriteria, $context);
    }
}
