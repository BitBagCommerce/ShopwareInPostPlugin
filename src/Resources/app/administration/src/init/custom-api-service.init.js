import CustomApiService from '../core/service/api/custom-api.service';

const Application = Shopware.Application;

Application.addServiceProvider('CustomApiService', (container) => {
    const initContainer = Application.getContainer('init');
    return new CustomApiService(initContainer.httpClient, container.loginService);
});
