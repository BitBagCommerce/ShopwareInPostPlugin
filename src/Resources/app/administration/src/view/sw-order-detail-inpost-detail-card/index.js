import template from './sw-order-detail-inpost-detail-card.html.twig';
import './sw-order-detail-inpost-detail-card.scss';

const { Component } = Shopware;

Component.register('sw-order-detail-inpost-detail-card', {
    template,
    inject: ['CustomApiService'],
    props: [
        'order'
    ],
    data() {
        return {
            showCard: false
        }
    },
    created() {
        const _this = this;

        const order = this.order;

        this.getInPostResponseData(order)
            .then((inPostResponse) => {
                if (inPostResponse) {
                    _this.showCard = true;
                }
            });
    },
    methods: {
        getInPostResponseData(order) {
            if (!order) {
                return;
            }

            if (order.extensions && order.extensions.inPost) {
                const pointName = order.extensions.inPost.pointName;

                if (pointName) {
                    return this.CustomApiService.getInpostDataByPointName(pointName)
                        .then((inPostResponse) => {
                            if (inPostResponse.data && inPostResponse.data.error) {
                                this.showCard = true;

                                return;
                            }

                            return inPostResponse.data;
                        });
                }
            }
        },
    }
});
