<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
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
        $criteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', CashPayment::class));

        return $this->paymentMethodRepository->searchIds($criteria, $context);
    }
}
