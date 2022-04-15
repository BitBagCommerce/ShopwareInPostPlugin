import template from './sw-order-detail-inpost-detail-card.html.twig';
import './sw-order-detail-inpost-detail-card.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-order-detail-inpost-detail-card', {
    template,
    inject: ['CustomApiService'],
    mixins: [
        Mixin.getByName('notification')
    ],

    async created() {
        await this.getOrder()
            .then((order) => {
                this.removeInPostDetailCardIfNotFoundInPost(order);

                this.getInPostResponseData(order)
                    .then((inPostResponse) => {
                        this.setInPostDetailsData(inPostResponse);
                    });

                if (!order || !order.extensions || !order.extensions.inPost || !order.extensions.inPost.packageId) {
                    const getLabelButtonEl = document.querySelector('.get-label');
                    if (getLabelButtonEl) {
                        getLabelButtonEl.classList.add('hide');
                    }
                }
            });
    },

    methods: {
        async createPackage() {
            await this.CustomApiService.createPackage(this.$route.params.id)
                .then(() => {
                    this.createNotificationSuccess({message: this.$tc('package.created')});

                    const getLabelButtonEl = document.querySelector('.get-label.hide');
                    if (getLabelButtonEl) {
                        getLabelButtonEl.classList.remove('hide');
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        const responseData = err.response.data;
                        if (responseData && responseData.errors && responseData.errors.length > 0) {
                            const error = responseData.errors[0];
                            if (error) {
                                this.createNotificationError({title: error.title, message: this.$tc(error.detail)})
                            }
                        }
                    }
                });
        },

        async getLabel() {
            await this.CustomApiService.getLabel(this.$route.params.id)
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
                                        this.createNotificationError({title: error.title, message: this.$tc(error.detail)})
                                    }
                                }
                            }
                        )
                    }
                });
        },

        async getOrder() {
            return this.CustomApiService.getOrder(this.$route.params.id)
                .then((response) => {
                    if (response.data && response.data.data) {
                        return response.data.data;
                    }
                })
                .catch((err) => {
                    console.error(err)
                });
        },

        async setInPostDetailsData(inPostResponseData) {
            if (!inPostResponseData) {
                return;
            }

            const addressDetails = inPostResponseData.address_details;

            const inpostDetailCardEl = document.querySelector('.inpost-details-card');
            if (inpostDetailCardEl) {
                inpostDetailCardEl.classList.remove('hide');

                const pointImageDivEl = document.getElementById('pointImageDiv');
                const pointImageEl = document.getElementById('pointImage');
                if (pointImageDivEl && pointImageEl) {
                    pointImageEl.src = inPostResponseData.image_url;
                    pointImageEl.alt = inPostResponseData.name;
                }

                const pointNameEl = document.getElementById('pointName');
                if (pointNameEl) {
                    pointNameEl.textContent = inPostResponseData.name;
                }

                const streetEl = document.getElementById('street');
                if (streetEl) {
                    streetEl.textContent = addressDetails.street;
                }

                const postCodeEl = document.getElementById('postCode');
                if (postCodeEl) {
                    postCodeEl.textContent = addressDetails.post_code;
                }

                const cityEl = document.getElementById('city');
                if (cityEl) {
                    cityEl.textContent = addressDetails.city;
                }

                const provinceEl = document.getElementById('province');
                if (provinceEl) {
                    provinceEl.textContent = addressDetails.province;
                }
            }
        },

        async getInPostResponseData(order) {
            if (!order) {
                return;
            }

            if(order.extensions && order.extensions.inPost) {
                const pointName = order.extensions.inPost.pointName;
                if(pointName) {
                    return this.CustomApiService.getInpostDataByPointName(pointName)
                        .then((inPostResponse) => {
                            if (inPostResponse.data && inPostResponse.data.error) {
                                const inpostDetailCardEl = document.querySelector('.inpost-details-card');
                                if (inpostDetailCardEl) {
                                    inpostDetailCardEl.remove();
                                }

                                return;
                            }

                            return inPostResponse.data;
                        });
                }
            }
        },

        async removeInPostDetailCardIfNotFoundInPost(order) {
            if (!order || !order.extensions.inPost || !order.extensions.inPost.pointName) {
                const inpostDetailCardEl = document.querySelector('.inpost-details-card');
                if (inpostDetailCardEl) {
                    inpostDetailCardEl.remove();
                }
            }
        }
    }
});