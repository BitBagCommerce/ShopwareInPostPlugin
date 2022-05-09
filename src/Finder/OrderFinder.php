<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderNotFoundException;
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
