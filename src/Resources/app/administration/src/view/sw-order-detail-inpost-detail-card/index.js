import template from './sw-order-detail-inpost-detail-card.html.twig';
import './sw-order-detail-inpost-detail-card.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-order-detail-inpost-detail-card', {
    template,
    inject: ['CustomApiService'],
    mixins: [
        Mixin.getByName('notification')
    ],

    created() {
        this.getOrder()
            .then((order) => {
                this.removeInPostDetailCardIfNotFoundInPost(order);

                this.getInPostResponseData(order)
                    .then((inPostResponse) => {
                        this.setInPostDetailsData(inPostResponse);
                    });

                this.hideGetLabelButtonIfNotFoundPackageId(order);

                this.hideCreatePackageButtonIfFoundPackage(order);
            });
    },

    methods: {
        createPackage() {
            this.CustomApiService.createPackage(this.$route.params.id)
                .then(() => {
                    this.createNotificationSuccess({message: this.$tc('package.created')});

                    const getLabelButtonEl = this.$refs.getLabel;

                    const createPackageButtonEl = this.$refs.createPackage;

                    if (getLabelButtonEl) {
                        getLabelButtonEl.classList.remove('hide');
                    }

                    if (createPackageButtonEl) {
                        createPackageButtonEl.remove();
                    }
                })
                .catch((err) => {
                    if (err.response && err.response.data) {
                        const responseData = err.response.data;

                        if (responseData && responseData.errors && responseData.errors.length > 0) {
                            const error = responseData.errors[0];

                            if (error) {
                                this.createNotificationError({
                                    title: error.title,
                                    message: this.$tc(error.detail).replace('%s', this.$route.params.id)
                                });
                            }
                        }
                    }
                });
        },

        getLabel() {
            const orderId = this.$route.params.id;

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
                                            title: error.title,
                                            message: this.$tc(error.detail).replace('%s', orderId)
                                        });
                                    }
                                }
                            }
                        )
                    }
                });
        },

        getOrder() {
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

        setInPostDetailsData(inPostResponseData) {
            if (!inPostResponseData) {
                return;
            }

            const addressDetails = inPostResponseData.address_details;

            const inpostDetailCardEl = this.$refs.inpostDetailsCard;

            if (inpostDetailCardEl) {
                inpostDetailCardEl.classList.remove('hide');

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
            }
        },

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
                                const inpostDetailCardEl = this.$refs.inpostDetailsCard;

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

        removeInPostDetailCardIfNotFoundInPost(order) {
            if (!order || !order.extensions.inPost || !order.extensions.inPost.pointName) {
                const inpostDetailCardEl = this.$refs.inpostDetailsCard;

                if (inpostDetailCardEl) {
                    inpostDetailCardEl.remove();
                }
            }
        },

        hideGetLabelButtonIfNotFoundPackageId(order) {
            if (!order || !order.extensions || !order.extensions.inPost || !order.extensions.inPost.packageId) {
                const getLabelButtonEl = this.$refs.getLabel;

                if (getLabelButtonEl) {
                    getLabelButtonEl.classList.add('hide');
                }
            }
        },

        hideCreatePackageButtonIfFoundPackage(order) {
            if (order && order.extensions && order.extensions.inPost && order.extensions.inPost.packageId) {
                const createPackageButtonEl = this.$refs.createPackage;

                if (createPackageButtonEl) {
                    createPackageButtonEl.remove();
                }
            }
        }
    }
});
