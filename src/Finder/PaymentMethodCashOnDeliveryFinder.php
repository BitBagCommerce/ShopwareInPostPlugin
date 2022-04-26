<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class PaymentMethodCashOnDeliveryFinder implements PaymentMethodCashOnDeliveryFinderInterface
{
    private EntityRepositoryInterface $paymentMethodRepository;

    public function __construct(EntityRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function find(Context $context): IdSearchResult
    {
        $paymentMethodCahOnDeliveryCriteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', CashPayment::class));

        return $this->paymentMethodRepository->searchIds($paymentMethodCahOnDeliveryCriteria, $context);
    }
}
