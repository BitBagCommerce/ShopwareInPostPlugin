<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Plugin;

use BitBag\ShopwareInPostPlugin\Exception\RuleNotFoundException;
use BitBag\ShopwareInPostPlugin\Factory\RulePayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\CashOnDeliveryPaymentMethodFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class RuleConfigurator implements RuleConfiguratorInterface
{
    private RuleFinderInterface $ruleFinder;

    private CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    private EntityRepositoryInterface $ruleRepository;

    public function __construct(
        RuleFinderInterface $ruleFinder,
        CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder,
        RulePayloadFactoryInterface $rulePayloadFactory,
        EntityRepositoryInterface $ruleRepository
    ) {
        $this->ruleFinder = $ruleFinder;
        $this->cashOnDeliveryPaymentMethodFinder = $cashOnDeliveryPaymentMethodFinder;
        $this->rulePayloadFactory = $rulePayloadFactory;
        $this->ruleRepository = $ruleRepository;
    }

    public function getRuleId(Context $context): string
    {
        $ruleName = RulePayloadFactoryInterface::DISABLE_PAYMENT_CASH_ON_DELIVERY;
        $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        if (0 === $rule->getTotal()) {
            $paymentMethodCahOnDelivery = $this->cashOnDeliveryPaymentMethodFinder->find($context);

            $paymentMethodId = $paymentMethodCahOnDelivery->firstId();
            if (null !== $paymentMethodId) {
                $rule = $this->rulePayloadFactory->create($ruleName, $paymentMethodId);

                $this->ruleRepository->create([$rule], $context);

                $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
            }
        }

        if (0 === $rule->getTotal()) {
            throw new RuleNotFoundException('rule.notFound');
        }

        $ruleId = $rule->firstId();

        if (null === $ruleId) {
            throw new RuleNotFoundException('rule.notFound');
        }

        return $ruleId;
    }
}
