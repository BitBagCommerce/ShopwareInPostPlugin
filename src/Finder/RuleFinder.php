<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
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
