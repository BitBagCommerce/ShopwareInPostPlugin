import template from './sw-order-detail-inpost-detail-card--get-label.html.twig';

const { Component, Mixin } = Shopware;

Component.register('sw-order-detail-inpost-detail-card--get-label', {
    template,
    inject: ['CustomApiService'],
    mixins: [
        Mixin.getByName('notification')
    ],
    props: [
        'order'
    ],
    data() {
        return {
            hideButton: true
        }
    },
    created() {
        this.$root.$on('getLabel.hideButton', (hideButton) => {
            this.hideButton = hideButton;
        });

        const order = this.order;

        if (order?.extensions?.inPost?.packageId) {
            this.hideButton = false;
        }
    },
    methods: {
        getLabel() {
            const orderId = this.order.id;

            this.CustomApiService.getLabel(orderId)
                .then((data) => {
                    const file = new Blob([data.data], {type: 'application/pdf'});
                    const blob = URL.createObjectURL(file);

                    window.open(blob, '_blank');

                    URL.revokeObjectURL(blob);
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        err.response.data.text()
                            .then((value) => {
                                    const parsedData = JSON.parse(value);

                                    if (parsedData && parsedData.errors && parsedData.errors.length > 0) {
                                        const error = parsedData.errors[0];

                                        if (error) {
                                            this.createNotificationError({
                                                message: this.$tc(error.detail).replace('%s', orderId)
                                            });
                                        }
                                    }
                                }
                            )
                    }
                });
        },
    }
});
