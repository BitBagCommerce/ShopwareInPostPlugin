const ApiService = Shopware.Classes.ApiService;

class CustomApiService extends ApiService {
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

    getInpostDataByPointName(pointName) {
        const apiRoute = `https://api-pl-points.easypack24.net/v1/points/${pointName}`;

        return this.httpClient.get(apiRoute);
    }
}

export default CustomApiService;
