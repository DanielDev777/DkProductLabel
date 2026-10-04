<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class ProductLabelProductDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'product_label_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function isVersionAware(): bool
    {
        return true;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('product_label_id', 'productLabelId', ProductLabelDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(ProductDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('productLabel', 'product_label_id', ProductLabelDefinition::class, 'id', false),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),
        ]);
    }
}
