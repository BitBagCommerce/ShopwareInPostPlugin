<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class CashOnDeliveryPaymentMethodFinder implements CashOnDeliveryPaymentMethodFinderInterface
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
