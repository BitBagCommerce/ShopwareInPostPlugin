import template from './sw-order-detail-inpost-detail-card--create-package.html.twig';

const { Component, Mixin } = Shopware;

Component.register('sw-order-detail-inpost-detail-card--create-package', {
    template,
    inject: ['InPostApiService'],
    mixins: [
        Mixin.getByName('notification')
    ],
    props: [
        'order'
    ],
    data() {
        return {
            showButton: true
        }
    },
    created() {
        const order = this.order;

        if (order?.extensions?.inPost?.packageId) {
            this.showButton = false;
        }
    },
    methods: {
        createPackage() {
            const orderId = this.order.id;

            this.InPostApiService.createPackage(orderId)
                .then(() => {
                    this.createNotificationSuccess({message: this.$tc('package.created')});

                    this.showButton = false;

                    this.$root.$emit('getLabel.hideButton', false);
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        const responseData = err.response.data;

                        if (responseData && responseData.errors && responseData.errors.length > 0) {
                            const error = responseData.errors[0];

                            if (error) {
                                this.createNotificationError({
                                    message: this.$tc(error.detail).replace('%s', orderId)
                                });
                            }
                        }
                    }
                });
        },
    }
});
