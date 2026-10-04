<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Tests\Integration\Core\Content\ProductLabel;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelCollection;
use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelEntity;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

class ProductLabelRepositoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testLabelCanBeWrittenAndReadWithProductAssignment(): void
    {
        $context = Context::createDefaultContext();
        $productId = $this->createProduct($context);
        $labelId = Uuid::randomHex();

        $this->labelRepository()->create([[
            'id' => $labelId,
            'color' => '#E3001B',
            'validFrom' => '2026-01-01 00:00:00',
            'translations' => [
                'en-GB' => ['name' => 'Sale'],
                'de-DE' => ['name' => 'Angebot'],
            ],
            'products' => [['id' => $productId]],
        ]], $context);

        $criteria = (new Criteria([$labelId]))
            ->addAssociation('translations')
            ->addAssociation('products');
        $label = $this->labelRepository()->search($criteria, $context)->getEntities()->first();

        static::assertInstanceOf(ProductLabelEntity::class, $label);
        static::assertSame('Sale', $label->getName());
        static::assertSame('#E3001B', $label->getColor());
        static::assertSame(0, $label->getPriority());
        static::assertTrue($label->isActive());
        static::assertSame('2026-01-01', $label->getValidFrom()?->format('Y-m-d'));
        static::assertNull($label->getValidTo());
        static::assertCount(2, $label->getTranslations() ?? []);

        $products = $label->getProducts();
        static::assertNotNull($products);
        static::assertSame([$productId], array_values($products->getIds()));

        $productCriteria = (new Criteria([$productId]))->addAssociation('productLabels');
        $product = $this->productRepository()->search($productCriteria, $context)->getEntities()->first();

        static::assertInstanceOf(ProductEntity::class, $product);
        $labels = $product->getExtension('productLabels');
        static::assertInstanceOf(ProductLabelCollection::class, $labels);
        static::assertSame([$labelId], array_values($labels->getIds()));
    }

    public function testInvalidColorIsRejected(): void
    {
        $this->expectException(WriteException::class);

        $this->labelRepository()->create([[
            'color' => 'red',
            'name' => 'Broken',
        ]], Context::createDefaultContext());
    }

    private function createProduct(Context $context): string
    {
        $id = Uuid::randomHex();

        $this->productRepository()->create([[
            'id' => $id,
            'productNumber' => 'DK-' . $id,
            'stock' => 10,
            'name' => 'Label test product',
            'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 10, 'net' => 8.4, 'linked' => false]],
            'tax' => ['name' => 'test', 'taxRate' => 19],
        ]], $context);

        return $id;
    }

    /**
     * @return EntityRepository<ProductLabelCollection>
     */
    private function labelRepository(): EntityRepository
    {
        $repository = static::getContainer()->get('product_label.repository');
        static::assertInstanceOf(EntityRepository::class, $repository);

        /** @var EntityRepository<ProductLabelCollection> $repository */
        return $repository;
    }

    /**
     * @return EntityRepository<ProductCollection>
     */
    private function productRepository(): EntityRepository
    {
        $repository = static::getContainer()->get('product.repository');
        static::assertInstanceOf(EntityRepository::class, $repository);

        /** @var EntityRepository<ProductCollection> $repository */
        return $repository;
    }
}
