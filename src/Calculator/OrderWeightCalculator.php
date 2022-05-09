<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Calculator;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderException;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    private EntityRepositoryInterface $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function calculate(OrderEntity $order, Context $context): float
    {
        $totalWeight = 0.0;

        $orderLineItems = $order->getLineItems();

        if (null === $orderLineItems) {
            throw new OrderException('order.productsNotFound');
        }

        $orderLineItems = $orderLineItems->getElements();

        $products = array_map(fn (OrderLineItemEntity $item) => $item->getProduct(), $orderLineItems);
        $products = array_filter($products);
        $parentIds = array_filter($products, fn (ProductEntity $product) => null !== $product->getParentId());
        $parentProducts = $this->productRepository->search(
            new Criteria(array_column($parentIds, 'parentId')),
            $context
        );
        $parentProducts = $parentProducts->getElements();

        foreach ($orderLineItems as $item) {
            $product = $item->getProduct();

            if (null !== $product) {
                $parentId = $product->getParentId();
                $productWeight = $product->getWeight();

                if (null !== $parentId && isset($parentProducts[$parentId])) {
                    /** @var ProductEntity $mainProduct */
                    $mainProduct = $parentProducts[$parentId];

                    $productWeight = $mainProduct->getWeight();
                }

                $totalWeight += $item->getQuantity() * $productWeight;
            }
        }

        return $totalWeight;
    }
}
