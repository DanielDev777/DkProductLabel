import template from './dk-product-label-detail.html.twig';

const { Mixin } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    props: {
        productLabelId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            productLabel: null,
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.productLabel, 'name');
        },

        productLabelRepository() {
            return this.repositoryFactory.create('product_label');
        },

        ...mapPropertyErrors('productLabel', [
            'name',
            'color',
            'priority',
        ]),
    },

    watch: {
        productLabelId() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.productLabelId) {
                this.loadEntityData();
                return;
            }

            Shopware.Store.get('context').resetLanguageToDefault();
            this.productLabel = this.productLabelRepository.create();
            this.productLabel.priority = 0;
            this.productLabel.active = true;
        },

        async loadEntityData() {
            this.isLoading = true;

            try {
                this.productLabel = await this.productLabelRepository.get(this.productLabelId);
            } finally {
                this.isLoading = false;
            }
        },

        abortOnLanguageChange() {
            return this.productLabelRepository.hasChanges(this.productLabel);
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        onChangeLanguage() {
            this.loadEntityData();
        },

        async onSave() {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            try {
                await this.productLabelRepository.save(this.productLabel);
                this.isSaveSuccessful = true;

                if (!this.productLabelId) {
                    this.$router.push({ name: 'dk.product.label.detail', params: { id: this.productLabel.id } });
                    return;
                }

                await this.loadEntityData();
            } catch {
                this.createNotificationError({
                    message: this.$t('dk-product-label.detail.messageSaveError'),
                });
            } finally {
                this.isLoading = false;
            }
        },

        onCancel() {
            this.$router.push({ name: 'dk.product.label.index' });
        },
    },
};
