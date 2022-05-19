import template from './sw-order-detail-inpost-detail-card--create-package.html.twig';

const { Component } = Shopware;

Component.register('sw-order-detail-inpost-detail-card--point-details', {
    template,
    inject: ['CustomApiService'],
    props: [
        'order'
    ],
    created() {
        const order = this.order;

        this.removeInPostDetailCardIfNotFoundInPost(order);

        this.getInPostResponseData(order)
            .then((inPostResponse) => {
                this.setInPostDetailsData(inPostResponse);
            });
    },
    methods: {
        setInPostDetailsData(inPostResponseData) {
            if (!inPostResponseData) {
                return;
            }

            const addressDetails = inPostResponseData.address_details;

            const pointImageDivEl = this.$refs.pointImageDiv;

            const pointImageEl = this.$refs.pointImage;

            if (pointImageDivEl && pointImageEl) {
                pointImageEl.src = inPostResponseData.image_url;
                pointImageEl.alt = inPostResponseData.name;
            }

            const pointNameEl = this.$refs.pointName;

            if (pointNameEl) {
                pointNameEl.textContent = inPostResponseData.name;
            }

            const streetEl = this.$refs.street;

            if (streetEl) {
                streetEl.textContent = addressDetails.street;
            }

            const postCodeEl = this.$refs.postCode;

            if (postCodeEl) {
                postCodeEl.textContent = addressDetails.post_code;
            }

            const cityEl = this.$refs.city;

            if (cityEl) {
                cityEl.textContent = addressDetails.city;
            }

            const provinceEl = this.$refs.province;

            if (provinceEl) {
                provinceEl.textContent = addressDetails.province;
            }
        },
        getInPostResponseData(order) {
            if (!order) {
                return;
            }

            if (order.extensions && order.extensions.inPost) {
                const pointName = order.extensions.inPost.pointName;

                if (pointName) {
                    return this.CustomApiService.getInPostDataByPointName(pointName)
                        .then((inPostResponse) => {
                            if (inPostResponse.data && inPostResponse.data.error) {
                                return;
                            }

                            return inPostResponse.data;
                        });
                }
            }
        },
        removeInPostDetailCardIfNotFoundInPost(order) {
            if (!order || !order.extensions.inPost || !order.extensions.inPost.pointName) {
                const inpostDetailCardEl = this.$refs.inpostDetailsCard;

                if (inpostDetailCardEl) {
                    inpostDetailCardEl.remove();
                }
            }
        },
    }
});
