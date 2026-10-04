<?php

declare(strict_types=1);

namespace Dk\ProductLabel;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class DkProductLabel extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container?->get(Connection::class);
        if (!$connection instanceof Connection) {
            throw new \RuntimeException('Database connection is not available.');
        }

        $connection->executeStatement('DROP TABLE IF EXISTS `product_label_product`, `product_label_translation`, `product_label`');

        $this->removeMigrations();
    }
}
