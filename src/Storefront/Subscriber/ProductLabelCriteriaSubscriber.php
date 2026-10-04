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
            ->addFilter($this->openEndedRange('validFrom', RangeFilter::LTE, $now))
            ->addFilter($this->openEndedRange('validTo', RangeFilter::GTE, $now))
            ->addSorting(new FieldSorting('priority', FieldSorting::DESCENDING));
    }

    /**
     * @param RangeFilter::LTE|RangeFilter::GTE $operator
     */
    private function openEndedRange(string $field, string $operator, string $value): MultiFilter
    {
        return new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter($field, null),
            new RangeFilter($field, [$operator => $value]),
        ]);
    }
}
