<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Tests\Unit\Storefront\Subscriber;

use Dk\ProductLabel\Storefront\Subscriber\ProductLabelCriteriaSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Shopware\Core\Content\Product\Events\ProductCrossSellingIdsCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductCrossSellingStreamCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSliderStaticCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSliderStreamCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(ProductLabelCriteriaSubscriber::class)]
class ProductLabelCriteriaSubscriberTest extends TestCase
{
    public function testAddsOnlyActiveAndCurrentlyValidLabelsSortedByPriority(): void
    {
        $subscriber = new ProductLabelCriteriaSubscriber(new MockClock('2026-10-01 12:00:00', 'UTC'));
        $criteria = new Criteria();

        $subscriber->addLabelAssociation($criteria);

        static::assertTrue($criteria->hasAssociation('productLabels'));
        $labelCriteria = $criteria->getAssociation('productLabels');

        static::assertEquals(
            [
                new EqualsFilter('active', true),
                new MultiFilter(MultiFilter::CONNECTION_OR, [
                    new EqualsFilter('validFrom', null),
                    new RangeFilter('validFrom', [RangeFilter::LTE => '2026-10-01 12:00:00.000']),
                ]),
                new MultiFilter(MultiFilter::CONNECTION_OR, [
                    new EqualsFilter('validTo', null),
                    new RangeFilter('validTo', [RangeFilter::GTE => '2026-10-01 12:00:00.000']),
                ]),
            ],
            $labelCriteria->getFilters(),
        );
        static::assertEquals(
            [new FieldSorting('priority', FieldSorting::DESCENDING)],
            $labelCriteria->getSorting(),
        );
    }

    public function testCompareDateIsConvertedToUtc(): void
    {
        $subscriber = new ProductLabelCriteriaSubscriber(new MockClock('2026-10-01 14:00:00', 'Europe/Berlin'));
        $criteria = new Criteria();

        $subscriber->addLabelAssociation($criteria);

        $validFromFilter = $criteria->getAssociation('productLabels')->getFilters()[1];
        static::assertInstanceOf(MultiFilter::class, $validFromFilter);
        static::assertEquals(
            new RangeFilter('validFrom', [RangeFilter::LTE => '2026-10-01 12:00:00.000']),
            $validFromFilter->getQueries()[1],
        );
    }

    /**
     * @return iterable<string, array{\Closure(Criteria, SalesChannelContext): object}>
     */
    public static function criteriaEventProvider(): iterable
    {
        yield 'product detail page' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductPageCriteriaEvent('product-id', $criteria, $context),
        ];
        yield 'product listing' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductListingCriteriaEvent(new Request(), $criteria, $context),
        ];
        yield 'search' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductSearchCriteriaEvent(new Request(), $criteria, $context),
        ];
        yield 'search suggest' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductSuggestCriteriaEvent(new Request(), $criteria, $context),
        ];
        yield 'cross selling by ids' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductCrossSellingIdsCriteriaEvent(new ProductCrossSellingEntity(), $criteria, $context),
        ];
        yield 'cross selling by stream' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductCrossSellingStreamCriteriaEvent(new ProductCrossSellingEntity(), $criteria, $context),
        ];
        yield 'static product slider' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductSliderStaticCriteriaEvent(new CmsSlotEntity(), $criteria, $context),
        ];
        yield 'stream product slider' => [
            static fn(Criteria $criteria, SalesChannelContext $context) => new ProductSliderStreamCriteriaEvent(new CmsSlotEntity(), $criteria, $context),
        ];
    }

    /**
     * @param \Closure(Criteria, SalesChannelContext): object $createEvent
     */
    #[DataProvider('criteriaEventProvider')]
    public function testSubscribedEventGetsLabelAssociation(\Closure $createEvent): void
    {
        $criteria = new Criteria();
        $event = $createEvent($criteria, $this->createStub(SalesChannelContext::class));
        $method = ProductLabelCriteriaSubscriber::getSubscribedEvents()[$event::class];

        (new ProductLabelCriteriaSubscriber(new MockClock()))->$method($event);

        static::assertTrue($criteria->hasAssociation('productLabels'));
    }
}
