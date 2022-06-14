import template from './bitbag-order-selected-items.html.twig';
import './bitbag-order-selected-items.scss';

Shopware.Component.register('bitbag-order-selected-items', {
    template,
    props: [
        'orders',
        'onCloseModal'
    ],
    inject: ['InPostApiService'],
    mixins: [
        'notification',
    ],
    data() {
        return {
            selectedOrders: []
        }
    },
    created() {
        const elements = [];

        Object.entries(JSON.parse(JSON.stringify(this.orders))).map((element) => {
            elements.push(element[1]);
        })

        this.selectedOrders = elements;
    },
    methods: {
        async submitForm(e) {
            const form = this.$refs.orderCourierForm;
            const badRequestStatus = 400;

            if (form.checkValidity()) {
                e.preventDefault();

                const data = new FormData(form);

                const formValues = Object.fromEntries(data.entries());

                const ordersIds = [];

                this.selectedOrders.map((order) => {
                    ordersIds.push(order.id);
                });

                try {
                    const response = await this.InPostApiService.orderCourier(ordersIds, formValues);

                    const responseData = response.data;

                    if (false === responseData.error) {
                        this.createNotificationSuccess({message: this.$tc(responseData.message)});

                        form.reset();
                    }
                } catch(err) {
                    if (badRequestStatus === err.response.status) {
                        this.createNotificationError({message: this.$tc(err.response.data.message)});
                    }
                }
            }
        }
    }
});
