import InPostApiService from '../core/service/api/inpost-api.service';

const Application = Shopware.Application;

Application.addServiceProvider('InPostApiService', (container) => {
    const initContainer = Application.getContainer('init');

    return new InPostApiService(initContainer.httpClient, container.loginService);
});
