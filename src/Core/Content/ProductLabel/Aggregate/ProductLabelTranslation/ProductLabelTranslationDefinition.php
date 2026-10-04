<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductLabelTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'product_label_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductLabelTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductLabelTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return ProductLabelDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
