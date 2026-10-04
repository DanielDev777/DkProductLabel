Shopware.Component.register('dk-product-label-list', () => import('./page/dk-product-label-list'));
Shopware.Component.register('dk-product-label-detail', () => import('./page/dk-product-label-detail'));
Shopware.Component.register('dk-product-detail-labels', () => import('./view/dk-product-detail-labels'));

Shopware.Module.register('dk-product-label', {
    type: 'plugin',
    name: 'product-label',
    title: 'dk-product-label.general.mainMenuItemGeneral',
    description: 'dk-product-label.general.descriptionTextModule',
    color: '#57D9A3',
    icon: 'regular-tag',
    entity: 'product_label',

    routeMiddleware(next, currentRoute) {
        const routeName = 'sw.product.detail.dkProductLabels';

        if (currentRoute.name === 'sw.product.detail' && !currentRoute.children.some((route) => route.name === routeName)) {
            currentRoute.children.push({
                name: routeName,
                path: '/sw/product/detail/:id?/labels',
                component: 'dk-product-detail-labels',
                meta: {
                    parentPath: 'sw.product.index',
                },
            });
        }

        next(currentRoute);
    },

    routes: {
        index: {
            component: 'dk-product-label-list',
            path: 'index',
        },
        create: {
            component: 'dk-product-label-detail',
            path: 'create',
            meta: {
                parentPath: 'dk.product.label.index',
            },
        },
        detail: {
            component: 'dk-product-label-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'dk.product.label.index',
            },
            props: {
                default(route) {
                    return {
                        productLabelId: route.params.id.toLowerCase(),
                    };
                },
            },
        },
    },

    navigation: [
        {
            id: 'dk-product-label',
            path: 'dk.product.label.index',
            label: 'dk-product-label.general.mainMenuItemGeneral',
            parent: 'sw-catalogue',
            color: '#57D9A3',
            position: 100,
        },
    ],
});
