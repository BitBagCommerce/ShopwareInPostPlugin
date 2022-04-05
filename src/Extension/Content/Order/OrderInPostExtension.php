<?php

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
            new OneToOneAssociationField('inPost', 'id', 'order_id', OrderInPostExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}
