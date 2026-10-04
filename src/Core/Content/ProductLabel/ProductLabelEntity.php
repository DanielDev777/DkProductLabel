<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel;

use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation\ProductLabelTranslationCollection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductLabelEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $name = null;

    protected string $color;

    protected int $priority;

    protected bool $active;

    protected ?\DateTimeInterface $validFrom = null;

    protected ?\DateTimeInterface $validTo = null;

    protected ?ProductLabelTranslationCollection $translations = null;

    protected ?ProductCollection $products = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function getTranslations(): ?ProductLabelTranslationCollection
    {
        return $this->translations;
    }

    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }
}
