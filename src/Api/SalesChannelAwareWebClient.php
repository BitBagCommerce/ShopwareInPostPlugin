<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Config\InPostConfigServiceInterface;

final class SalesChannelAwareWebClient implements SalesChannelAwareWebClientInterface
{
    private WebClientInterface $webClient;

    private InPostConfigServiceInterface $inPostConfigService;

    public function __construct(WebClientInterface $webClient, InPostConfigServiceInterface $inPostConfigService)
    {
        $this->webClient = $webClient;
        $this->inPostConfigService = $inPostConfigService;
    }

    public function createShipment(array $data, string $salesChannelId): array
    {
        $package = $this->webClient->request(
            self::METHOD_POST,
            $this->getApiEndpointForShipment($salesChannelId),
            $this->getHeaders($this->getApiConfig($salesChannelId)['accessToken']),
            $data,
        );

        return json_decode($package, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function getLabelByShipmentId(int $shipmentId, ?string $salesChannelId = null): string
    {
        $url = sprintf(self::SHIPMENTS_LABEL_URL, $this->webClient->getApiBaseUrl($this->isSandbox($salesChannelId)), $shipmentId);

        return $this->webClient->request(
            'GET',
            $url,
            $this->getHeaders($this->getApiConfig($salesChannelId)['accessToken'])
        );
    }

    public function orderCourier(array $data, ?string $salesChannelId = null): array
    {
        $result = $this->webClient->request(
            self::METHOD_POST,
            $this->getApiEndpointForDispatchOrders($salesChannelId),
            $this->getHeaders($this->getApiConfig($salesChannelId)['accessToken']),
            $data,
        );

        return json_decode($result, true, 512, \JSON_THROW_ON_ERROR);
    }

    private function getHeaders(string $accessToken): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $accessToken),
        ];
    }

    private function isSandbox(?string $salesChannelId = null): bool
    {
        return WebClientInterface::SANDBOX_ENVIRONMENT === $this->getApiConfig($salesChannelId)['environment'];
    }

    private function getApiEndpointForShipment(?string $salesChannelId = null): string
    {
        return sprintf(
            self::SHIPMENTS_ORGANIZATIONS_URL,
            $this->webClient->getApiBaseUrl($this->isSandbox($salesChannelId)),
            $this->getApiConfig($salesChannelId)['organizationId']
        );
    }

    private function getApiEndpointForDispatchOrders(?string $salesChannelId = null): string
    {
        return sprintf(
            self::ORGANIZATIONS_DISPATCH_ORDERS,
            $this->webClient->getApiBaseUrl($this->isSandbox($salesChannelId)),
            $this->getApiConfig($salesChannelId)['organizationId']
        );
    }

    private function getApiConfig(?string $salesChannelId = null): array
    {
        $inPostApiConfig = $this->inPostConfigService->getInPostApiConfig($salesChannelId);

        return [
            'accessToken' => $inPostApiConfig->getAccessToken(),
            'organizationId' => $inPostApiConfig->getOrganizationId(),
            'environment' => $inPostApiConfig->getEnvironment(),
        ];
    }
}
