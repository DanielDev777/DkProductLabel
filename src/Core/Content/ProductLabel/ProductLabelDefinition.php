<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel;

use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation\ProductLabelTranslationDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductLabelDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'product_label';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductLabelEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'priority' => 0,
            'active' => true,
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new TranslatedField('name'))->addFlags(new ApiAware()),
            (new StringField('color', 'color', 7))->addFlags(new ApiAware(), new Required()),
            (new IntField('priority', 'priority'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new DateTimeField('valid_from', 'validFrom'))->addFlags(new ApiAware()),
            (new DateTimeField('valid_to', 'validTo'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(ProductLabelTranslationDefinition::class, 'product_label_id'))
                ->addFlags(new ApiAware(), new Required()),
            (new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                ProductLabelProductDefinition::class,
                'product_label_id',
                'product_id',
            ))->addFlags(new ApiAware()),
        ]);
    }
}
