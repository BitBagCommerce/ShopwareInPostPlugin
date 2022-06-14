const ApiService = Shopware.Classes.ApiService;

class InPostApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '') {
        super(httpClient, loginService, apiEndpoint);
    }

    createPackage(orderId) {
        const apiRoute = `${this.getApiBasePath()}/_action/bitbag-inpost-plugin/package/${orderId}`;

        return this.httpClient
            .post(apiRoute, {}, {
                headers: this.getBasicHeaders()
            })
            .then((response) => {
                if (201 === response.status) {
                    return ApiService.handleResponse(response);
                }
            });
    }

    getLabel(orderId) {
        const apiRoute = `${this.getApiBasePath()}/_action/bitbag-inpost-plugin/label/${orderId}`;

        return this.httpClient
            .get(apiRoute, { responseType: 'blob' }, {
                headers: this.getBasicHeaders()
            });
    }

    getOrder(orderId) {
        const apiRoute = `${this.getApiBasePath()}/order/${orderId}`;

        return this.httpClient
            .get(apiRoute, {}, {
                headers: this.getBasicHeaders()
            });
    }

    getInPostDataByPointName(pointName) {
        const apiRoute = `https://api-pl-points.easypack24.net/v1/points/${pointName}`;

        return this.httpClient.get(apiRoute);
    }

    checkCredentials(values) {
        const apiRoute = `${this.getApiBasePath()}/_action/bitbag-inpost-plugin/check-credentials`;

        return this.httpClient
            .post(apiRoute, values, {
                headers: this.getBasicHeaders()
            });
    }

    orderCourier(ordersIds, formValues) {
        const apiRoute = `${this.getApiBasePath()}/_action/bitbag-inpost-plugin/order-courier`;

        return this.httpClient
            .post(apiRoute, {ordersIds, formValues}, {
                headers: this.getBasicHeaders()
            });
    }
}

export default InPostApiService;
