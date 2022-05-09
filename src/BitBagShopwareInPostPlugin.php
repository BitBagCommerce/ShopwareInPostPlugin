<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Plugin\CustomFieldSetConfiguratorInterface;
use BitBag\ShopwareInPostPlugin\Plugin\RuleConfiguratorInterface;
use BitBag\ShopwareInPostPlugin\Plugin\ShippingMethodConfiguratorInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;

final class BitBagShopwareInPostPlugin extends Plugin
{
    private CustomFieldSetConfiguratorInterface $customFieldSetConfigurator;

    private RuleConfiguratorInterface $ruleConfigurator;

    private ShippingMethodConfiguratorInterface $shippingMethodConfigurator;

    public function setCustomFieldSetConfigurator(CustomFieldSetConfiguratorInterface $customFieldSetConfigurator): void
    {
        $this->customFieldSetConfigurator = $customFieldSetConfigurator;
    }

    public function setRuleConfigurator(RuleConfiguratorInterface $ruleConfigurator): void
    {
        $this->ruleConfigurator = $ruleConfigurator;
    }

    public function setShippingMethodConfigurator(ShippingMethodConfiguratorInterface $shippingMethodConfigurator): void
    {
        $this->shippingMethodConfigurator = $shippingMethodConfigurator;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $context = $activateContext->getContext();

        $ruleId = $this->ruleConfigurator->getRuleId($context);

        $this->shippingMethodConfigurator->createShippingMethod($ruleId, $context);
        $this->shippingMethodConfigurator->toggleActiveShippingMethod(true, $context);
        $this->customFieldSetConfigurator->createCustomFieldSetForPackageDetails($context);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->shippingMethodConfigurator->toggleActiveShippingMethod(false, $deactivateContext->getContext());
    }
}
