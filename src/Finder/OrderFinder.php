<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Exception\OrderNotFoundException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class OrderFinder implements OrderFinderInterface
{
    private EntityRepositoryInterface $orderRepository;

    public function __construct(EntityRepositoryInterface $orderRepository)
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
        ]);

        $order = $this->orderRepository->search($orderCriteria, $context)->first();

        if (null === $order) {
            throw new OrderNotFoundException('order.notFound');
        }

        return $order;
    }
}
