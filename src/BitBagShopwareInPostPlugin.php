<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Exception\RuleNotFoundException;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\RulePayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\PackageDetailsCustomFieldSetFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\PaymentMethodCashOnDeliveryFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\RuleFinderInterface;
use BitBag\ShopwareInPostPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

final class BitBagShopwareInPostPlugin extends Plugin
{
    private EntityRepositoryInterface $shippingMethodRepository;

    private ShippingMethodPayloadFactoryInterface $shippingMethodFactory;

    private ShippingMethodFinderInterface $shippingMethodFinder;

    private EntityRepositoryInterface $customFieldSetRepository;

    private CustomFieldsForPackageDetailsPayloadFactory $customFieldsForPackageDetailsPayloadFactory;

    private PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder;

    private RuleFinderInterface $ruleFinder;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    private EntityRepositoryInterface $ruleRepository;

    private PaymentMethodCashOnDeliveryFinderInterface $paymentMethodCashOnDeliveryFinder;

    public function setShippingMethodRepository(EntityRepositoryInterface $shippingMethodRepository): void
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function setShippingMethodFactory(ShippingMethodPayloadFactoryInterface $shippingMethodFactory): void
    {
        $this->shippingMethodFactory = $shippingMethodFactory;
    }

    public function setShippingMethodFinder(ShippingMethodFinderInterface $shippingMethodFinder): void
    {
        $this->shippingMethodFinder = $shippingMethodFinder;
    }

    public function setCustomFieldSetRepository(EntityRepositoryInterface $customFieldSetRepository): void
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function setCustomFieldsForPackageDetailsPayloadFactory(
        CustomFieldsForPackageDetailsPayloadFactory $customFieldsForPackageDetailsPayloadFactory
    ): void {
        $this->customFieldsForPackageDetailsPayloadFactory = $customFieldsForPackageDetailsPayloadFactory;
    }

    public function setPackageDetailsCustomFieldSetFinder(
        PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder
    ): void {
        $this->packageDetailsCustomFieldSetFinder = $packageDetailsCustomFieldSetFinder;
    }

    public function setRuleFinder(RuleFinderInterface $ruleFinder): void
    {
        $this->ruleFinder = $ruleFinder;
    }

    public function setRulePayloadFactory(RulePayloadFactoryInterface $rulePayloadFactory): void
    {
        $this->rulePayloadFactory = $rulePayloadFactory;
    }

    public function setRuleRepository(EntityRepositoryInterface $ruleRepository): void
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function setPaymentMethodCashOnDeliveryFinder(
        PaymentMethodCashOnDeliveryFinderInterface $paymentMethodCashOnDeliveryFinder
    ): void {
        $this->paymentMethodCashOnDeliveryFinder = $paymentMethodCashOnDeliveryFinder;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $context = $activateContext->getContext();

        $this->createShippingMethod($context);
        $this->toggleActiveShippingMethod(true, $context);
        $this->createCustomFieldSetForPackageDetails($context);
        $this->setActiveCustomFieldSetForPackageDetails(true, $context);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->toggleActiveShippingMethod(false, $deactivateContext->getContext());
        $this->setActiveCustomFieldSetForPackageDetails(false, $deactivateContext->getContext());
    }

    private function createShippingMethod(Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchIdsByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            return;
        }

        $inPostShippingMethod = $this->shippingMethodFactory->create(
            ShippingMethodPayloadFactoryInterface::SHIPPING_KEY,
            $this->getRuleId($context),
            $context
        );

        $this->shippingMethodRepository->create([$inPostShippingMethod], $context);
    }

    private function toggleActiveShippingMethod(bool $active, Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();

            $this->shippingMethodRepository->update([
                [
                    'id' => $firstShippingMethod->getId(),
                    'active' => $active,
                ],
            ], $context);
        }
    }

    private function createCustomFieldSetForPackageDetails(Context $context): void
    {
        $customFields = $this->packageDetailsCustomFieldSetFinder->search($context);

        if (0 !== $customFields->getTotal()) {
            return;
        }

        $data = $this->customFieldsForPackageDetailsPayloadFactory->create();

        $this->customFieldSetRepository->upsert([$data], $context);
    }

    private function setActiveCustomFieldSetForPackageDetails(bool $active, Context $context): void
    {
        $customFields = $this->packageDetailsCustomFieldSetFinder->search($context);

        if (0 === $customFields->getTotal()) {
            return;
        }

        $this->customFieldSetRepository->update([
            [
                'id' => $customFields->firstId(),
                'active' => $active,
            ],
        ], $context);
    }

    private function getRuleId(Context $context): string
    {
        $ruleName = 'Disable payment cash on delivery';
        $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        if (0 === $rule->getTotal()) {
            $paymentMethodCahOnDelivery = $this->paymentMethodCashOnDeliveryFinder->find($context);

            if (0 < $paymentMethodCahOnDelivery->getTotal()) {
                $paymentMethodId = $paymentMethodCahOnDelivery->firstId();
                if (null !== $paymentMethodId) {
                    $rule = $this->rulePayloadFactory->create($ruleName, $paymentMethodId);

                    $this->ruleRepository->create([$rule], $context);

                    $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
                }
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
