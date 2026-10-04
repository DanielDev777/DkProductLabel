<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Storefront\Subscriber;

use Psr\Clock\ClockInterface;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds the product labels to every storefront product query (detail page, listing, search, suggest),
 * already filtered to active, currently valid labels and sorted by priority, so templates only render.
 */
class ProductLabelCriteriaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class => 'onProductCriteria',
            ProductListingCriteriaEvent::class => 'onProductCriteria',
            ProductSearchCriteriaEvent::class => 'onProductCriteria',
            ProductSuggestCriteriaEvent::class => 'onProductCriteria',
        ];
    }

    public function onProductCriteria(ProductPageCriteriaEvent|ProductListingCriteriaEvent $event): void
    {
        $this->addLabelAssociation($event->getCriteria());
    }

    public function addLabelAssociation(Criteria $criteria): void
    {
        $now = $this->clock->now()
            ->setTimezone(new \DateTimeZone('UTC'))
            ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $criteria->getAssociation('productLabels')
            ->addFilter(new EqualsFilter('active', true))
            ->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('validFrom', null),
                new RangeFilter('validFrom', [RangeFilter::LTE => $now]),
            ]))
            ->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsFilter('validTo', null),
                new RangeFilter('validTo', [RangeFilter::GTE => $now]),
            ]))
            ->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING));
    }
}
