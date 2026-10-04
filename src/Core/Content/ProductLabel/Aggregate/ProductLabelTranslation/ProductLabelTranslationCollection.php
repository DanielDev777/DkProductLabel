<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ProductLabelTranslationEntity>
 */
class ProductLabelTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'product_label_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }
}