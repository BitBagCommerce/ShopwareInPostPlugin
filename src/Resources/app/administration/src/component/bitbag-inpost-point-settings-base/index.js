import template from './bitbag-inpost-point-settings-base.html.twig';

Shopware.Component.register('bitbag-inpost-point-settings-base', {
    template,
    inject: [
        'systemConfigApiService',
        'InPostApiService'
    ],
    mixins: [
        'notification',
    ],
    data() {
        return {
            isLoading: true,
            pluginDomain: 'bitb1BitBagShopwareInPostPlugin',
            organizationId: '',
            accessToken: '',
            environment: '',
            salesChannel: null
        }
    },
    created() {
        this.isLoading = false;
    },
    methods: {
        checkCredentials() {
            const systemConfig = this.$refs.systemConfig;
            const actualConfigData = systemConfig.actualConfigData;
            const currentSalesChannelId = systemConfig.currentSalesChannelId;
            const dataPrefix = [this.pluginDomain] + ['.inPost'];

            let accessToken = actualConfigData[currentSalesChannelId][[dataPrefix] + ['AccessToken']];

            if (null === accessToken || undefined === accessToken) {
                accessToken = actualConfigData.null[[dataPrefix] + ['AccessToken']];
            }

            let organizationId = actualConfigData[currentSalesChannelId][[dataPrefix] + ['OrganizationId']];

            if (null === organizationId || undefined === organizationId) {
                organizationId = actualConfigData.null[[dataPrefix] + ['OrganizationId']];
            }

            let environment = actualConfigData[currentSalesChannelId][[dataPrefix] + ['Environment']];

            if (null === environment || undefined === environment) {
                environment = actualConfigData.null[[dataPrefix] + ['Environment']];
            }

            const values = {
                accessToken,
                organizationId,
                environment
            };

            const responseStatusCodeOk = 200;
            const responseStatusCodeForbidden = 403;

            this.InPostApiService.checkCredentials(values)
                .then((data) => {
                    if (responseStatusCodeOk === data.status) {
                        this.createNotificationSuccess({message: this.$tc('api.credentialsDataOk')});
                    }
                })
                .catch((err) => {
                    if (responseStatusCodeForbidden === err.response.status) {
                        this.createNotificationError({
                            message: this.$tc('api.providedDataNotValid')
                        });

                        return;
                    }

                    if (err.response && err.response.data) {
                        const responseData = err.response.data;

                        if (responseData && responseData.errors && responseData.errors.length > 0) {
                            const error = responseData.errors[0];

                            if (error) {
                                this.createNotificationError({
                                    message: this.$tc(error.detail)
                                });

                                return;
                            }
                        }
                    }

                    console.error(err);
                });
        },
        saveSystemConfig() {
            this.$refs.systemConfig.saveAll();

            this.createNotificationSuccess({message: this.$tc('config.saved')});
        }
    }
});
