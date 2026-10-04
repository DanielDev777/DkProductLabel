<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\Product;

use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductLabelExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'productLabels',
                ProductLabelDefinition::class,
                ProductLabelProductDefinition::class,
                'product_id',
                'product_label_id',
            ))->addFlags(new ApiAware(), new CascadeDelete()),
        );
    }

    public function getEntityName(): string
    {
        return ProductDefinition::ENTITY_NAME;
    }
}
