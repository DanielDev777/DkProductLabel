import template from './dk-product-detail-labels.html.twig';

export default {
    template,

    computed: {
        product() {
            return Shopware.Store.get('swProductDetail').product;
        },

        isLoading() {
            return Shopware.Store.get('swProductDetail').isLoading;
        },
    },
};
