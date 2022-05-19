import './init/custom-api-service.init';
import './view/sw-order-detail-inpost-detail-card';
import './view/sw-order-detail-inpost-detail-card--create-package';
import './view/sw-order-detail-inpost-detail-card--get-label';
import './view/sw-order-detail-inpost-detail-card--point-details';
import './extension/sw-order/sw-order-detail-base';

import './component/bitbag-inpost-point-settings-base';
import './component/bitbag-inpost-point-settings-icon';

Shopware.Module.register('bitbag-inpost-point', {
    type: 'plugin',
    name: 'InPost settings',
    title: 'InPost settings',
    description: 'InPost settings',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#FFD700',
    icon: 'default-action-settings',
    routes: {
        index: {
            component: 'bitbag-inpost-point-settings-base',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
            },
        },
    },
    settingsItem: {
        group: 'plugins',
        to: 'bitbag.inpost.point.index',
        iconComponent: 'bitbag-inpost-point-settings-icon',
        backgroundEnabled: false,
    },
});

