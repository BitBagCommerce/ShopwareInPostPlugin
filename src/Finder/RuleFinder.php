<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class RuleFinder implements RuleFinderInterface
{
    private EntityRepositoryInterface $ruleRepository;

    public function __construct(EntityRepositoryInterface $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function getRuleIdsByName(string $name, Context $context): IdSearchResult
    {
        $ruleCriteria = (new Criteria())->addFilter(new EqualsFilter('name', $name));

        return $this->ruleRepository->searchIds($ruleCriteria, $context);
    }
}
