<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class ProductLabelTranslationEntity extends TranslationEntity
{
    protected string $productLabelId;

    protected ?string $name = null;

    protected ?ProductLabelEntity $productLabel = null;

    public function getProductLabelId(): string
    {
        return $this->productLabelId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getProductLabel(): ?ProductLabelEntity
    {
        return $this->productLabel;
    }
}