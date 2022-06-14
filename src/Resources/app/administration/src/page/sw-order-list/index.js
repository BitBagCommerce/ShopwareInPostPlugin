import template from './sw-order-list.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.override('sw-order-list', {
    template,
    mixins: [
        'notification',
    ],
    data() {
        return {
            orderCourierInPostData: {
                label: {
                    'en-GB': 'Order courier (InPost)',
                    'pl-PL': 'Zamów kuriera (InPost)'
                },
                showModal: false,
                renderOrderSelectedItems: false
            }
        }
    },
    computed: {
        orderCriteria() {
            const criteria = new Criteria(this.page, this.limit);

            criteria.setTerm(this.term);

            this.sortBy.split(',').forEach(sortBy => {
                criteria.addSorting(Criteria.sort(sortBy, this.sortDirection));
            });

            this.filterCriteria.forEach(filter => {
                criteria.addFilter(filter);
            });

            criteria.addAssociation('addresses');
            criteria.addAssociation('billingAddress');
            criteria.addAssociation('salesChannel');
            criteria.addAssociation('orderCustomer');
            criteria.addAssociation('currency');
            criteria.addAssociation('documents');
            criteria.addAssociation('transactions');
            criteria.addAssociation('deliveries');
            criteria.getAssociation('transactions').addSorting(Criteria.sort('createdAt'));
            criteria.getAssociation('deliveries.shippingMethod');
            criteria.getAssociation('inPost');

            return criteria;
        },
    },
    methods: {
        orderCourierInPostAction() {
            const selectedOrders = JSON.parse(JSON.stringify(this.$refs.orderGrid.selection));

            if (0 === Object.entries(selectedOrders).length) {
                this.createNotificationError({message: 'Select order before order an courier to pickup packages'});
                return;
            }

            this.orderCourierInPostData.showModal = false

            this.orderCourierInPostData.renderOrderSelectedItems = true;
        },
        onCloseModal() {
            this.orderCourierInPostData.renderOrderSelectedItems = false;
        }
    }
});
