<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1669886058OrderInPostExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1669886058;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `bitbag_inpost_point_order_extension`
    ADD COLUMN `sending_method` VARCHAR(255) NULL');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
