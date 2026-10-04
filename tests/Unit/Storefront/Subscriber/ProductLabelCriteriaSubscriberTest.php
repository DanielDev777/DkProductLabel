<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Tests\Unit\Storefront\Subscriber;

use Dk\ProductLabel\Storefront\Subscriber\ProductLabelCriteriaSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
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
    public function testSubscribesToAllStorefrontProductCriteriaEvents(): void
    {
        static::assertSame(
            [
                ProductPageCriteriaEvent::class,
                ProductListingCriteriaEvent::class,
                ProductSearchCriteriaEvent::class,
                ProductSuggestCriteriaEvent::class,
            ],
            array_keys(ProductLabelCriteriaSubscriber::getSubscribedEvents()),
        );
    }

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

    public function testListingEventCriteriaGetsLabelAssociation(): void
    {
        $criteria = new Criteria();
        $event = new ProductListingCriteriaEvent(new Request(), $criteria, $this->createStub(SalesChannelContext::class));

        (new ProductLabelCriteriaSubscriber(new MockClock()))->onProductCriteria($event);

        static::assertTrue($criteria->hasAssociation('productLabels'));
    }

    public function testProductPageEventCriteriaGetsLabelAssociation(): void
    {
        $criteria = new Criteria();
        $event = new ProductPageCriteriaEvent('product-id', $criteria, $this->createStub(SalesChannelContext::class));

        (new ProductLabelCriteriaSubscriber(new MockClock()))->onProductCriteria($event);

        static::assertTrue($criteria->hasAssociation('productLabels'));
    }
}
