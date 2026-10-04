<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ProductLabelEntity>
 */
class ProductLabelCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'product_label_collection';
    }

    protected function getExpectedClass(): string
    {
        return ProductLabelEntity::class;
    }
}
