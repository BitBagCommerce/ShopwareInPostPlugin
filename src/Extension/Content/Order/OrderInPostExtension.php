<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Extension\Content\Order;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class OrderInPostExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                OrderInPostExtensionInterface::PROPERTY_KEY,
                'id',
                'order_id',
                OrderInPostExtensionDefinition::class,
                true
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}
