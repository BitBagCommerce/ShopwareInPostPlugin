<?php

declare(strict_types=1);

namespace BitBag\InPost\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

class ShippingParametersFinder
{
    private EntityRepositoryInterface $deliveryTimeRepository;

    private EntityRepositoryInterface $ruleRepository;

    public function __construct(
        EntityRepositoryInterface $deliveryTimeRepository,
        EntityRepositoryInterface $ruleRepository
    ) {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->ruleRepository = $ruleRepository;
    }

    public function getDeliveryTimeIds(Context $context): IdSearchResult
    {
        $deliveryTimeCriteria = (new Criteria())
            ->addFilter(new ContainsFilter('unit', 'day'))
            ->addFilter(new EqualsFilter('min', 1))
            ->addFilter(new EqualsFilter('max', 3));

        return $this->deliveryTimeRepository->searchIds($deliveryTimeCriteria, $context);
    }

    public function getRuleIds(Context $context): IdSearchResult
    {
        $ruleCriteria = (new Criteria())->addFilter(new EqualsFilter('name', 'Cart >= 0'));

        $rule = $this->ruleRepository->searchIds($ruleCriteria, $context);
        if (0 === $rule->getTotal()) {
            $ruleCriteria = (new Criteria());

            $rule = $this->ruleRepository->searchIds($ruleCriteria, $context);
        }

        return $rule;
    }
}
