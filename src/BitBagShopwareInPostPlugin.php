<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin;

use BitBag\ShopwareInPostPlugin\Config\InPostConfigServiceInterface;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionDefinition;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Plugin\CustomFieldSetConfiguratorInterface;
use BitBag\ShopwareInPostPlugin\Plugin\RuleConfiguratorInterface;
use BitBag\ShopwareInPostPlugin\Plugin\ShippingMethodConfiguratorInterface;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

final class BitBagShopwareInPostPlugin extends Plugin
{
    private CustomFieldSetConfiguratorInterface $customFieldSetConfigurator;

    private RuleConfiguratorInterface $ruleConfigurator;

    private ShippingMethodConfiguratorInterface $shippingMethodConfigurator;

    private Connection $connection;

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

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
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

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $db = $this->connection;

        // These are actually only tables from old plugin versions. We still remove them here just in case.
        $db->executeStatement('DROP TABLE IF EXISTS `' . OrderInPostExtensionDefinition::ENTITY_NAME . '`;');

        $db->executeStatement(
            'DELETE FROM system_config
            WHERE configuration_key LIKE :domain',
            [
                'domain' => InPostConfigServiceInterface::SYSTEM_CONFIG_PREFIX . '.%',
            ],
        );

        $db->executeStatement(
            'DELETE FROM custom_field_set where JSON_EXTRACT(config, "$.technical_name") = :technicalName',
            [
                'technicalName' => CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY,
            ],
        );

        $this->shippingMethodConfigurator->toggleActiveShippingMethod(false, $uninstallContext->getContext());
    }
}
