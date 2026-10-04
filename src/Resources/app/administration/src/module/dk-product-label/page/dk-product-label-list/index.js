import template from './dk-product-label-list.html.twig';
import './dk-product-label-list.scss';

const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            productLabels: null,
            isLoading: true,
            sortBy: 'priority',
            sortDirection: 'DESC',
            total: 0,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        productLabelRepository() {
            return this.repositoryFactory.create('product_label');
        },

        productLabelCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            return criteria;
        },

        productLabelColumns() {
            return [
                {
                    property: 'name',
                    label: 'dk-product-label.list.columnName',
                    routerLink: 'dk.product.label.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'color',
                    label: 'dk-product-label.list.columnColor',
                    allowResize: true,
                },
                {
                    property: 'priority',
                    label: 'dk-product-label.list.columnPriority',
                    inlineEdit: 'number',
                    allowResize: true,
                    align: 'right',
                },
                {
                    property: 'active',
                    label: 'dk-product-label.list.columnActive',
                    inlineEdit: 'boolean',
                    allowResize: true,
                    align: 'center',
                },
                {
                    property: 'validFrom',
                    label: 'dk-product-label.list.columnValidFrom',
                    allowResize: true,
                },
                {
                    property: 'validTo',
                    label: 'dk-product-label.list.columnValidTo',
                    allowResize: true,
                },
            ];
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },
    },

    methods: {
        async getList() {
            this.isLoading = true;

            try {
                const result = await this.productLabelRepository.search(this.productLabelCriteria);
                this.productLabels = result;
                this.total = result.total;
            } finally {
                this.isLoading = false;
            }
        },

        onChangeLanguage() {
            this.getList();
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
};
