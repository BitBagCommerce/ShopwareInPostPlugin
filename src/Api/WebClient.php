<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Config\InpostConfigServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

final class WebClient implements WebClientInterface
{
    private Client $apiClient;

    private string $organizationId;

    private string $accessToken;

    private string $environment;

    public function __construct(Client $client, InpostConfigServiceInterface $inpostConfigService)
    {
        $inpostApiConfig = $inpostConfigService->getInpostApiConfig();

        $this->apiClient = $client;
        $this->accessToken = $inpostApiConfig->getAccessToken();
        $this->organizationId = $inpostApiConfig->getOrganizationId();
        $this->environment = $inpostApiConfig->getEnvironment();
    }

    public function getPointByName(string $name, int $attempts = 0): ?array
    {
        $url = $this->getApiEndpointForPointByName($name);

        try {
            $request = $this->request('GET', $url);

            return json_decode($request, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Exception $exception) {
            if ($attempts < 3) {
                sleep(1);

                return $this->getPointByName($name, ($attempts + 1));
            }
        }

        return null;
    }

    public function getOrganizations(): array
    {
        $url = $this->getApiEndpointForOrganizations();

        $request = $this->request('GET', $url);

        return json_decode($request, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function getShipmentById(int $id): ?array
    {
        $url = $this->getApiEndpointForShipmentById($id);

        $request = $this->request('GET', $url);

        return json_decode($request, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function getLabels(array $shipmentIds): ?string
    {
        $url = $this->getApiEndpointForLabels();

        $data = [
            'format' => 'pdf',
            'shipment_ids' => $shipmentIds,
        ];

        return $this->request('POST', $url, $data);
    }

    public function getShipments(): ?array
    {
        $url = $this->getApiEndpointForShipment();

        $request = $this->request('GET', $url);

        return json_decode($request, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function createShipment(array $data): array
    {
        $package = $this->request('POST', $this->getApiEndpointForShipment(), $data);

        return json_decode($package, true, 512, \JSON_THROW_ON_ERROR);
    }

    public function request(string $method, string $url, array $data = []): string
    {
        $options = [
            'json' => $data,
            'headers' => $this->getAuthorizedHeaderWithContentType(),
        ];

        try {
            $result = $this->apiClient->request($method, $url, $options);
        } catch (ClientException $exception) {
            /** @var ResponseInterface $result */
            $result = $exception->getResponse();

            throw new ClientException(
                (string) $result->getBody(),
                $exception->getRequest(),
                $result
            );
        }

        return (string) $result->getBody();
    }

    public function getLabelByShipmentId(int $shipmentId): string
    {
        $url = sprintf('%s/shipments/%s/label', $this->getApiEndpoint(), $shipmentId);

        return $this->request('GET', $url, []);
    }

    private function getAuthorizedHeaderWithContentType(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->accessToken),
        ];
    }

    private function getApiEndpoint(): string
    {
        $apiEndpoint = self::SANDBOX_ENVIRONMENT === $this->environment ? self::SANDBOX_API_ENDPOINT : self::PRODUCTION_API_ENDPOINT;

        return sprintf('%s/%s', $apiEndpoint, self::API_VERSION);
    }

    private function getApiEndpointForShipment(): string
    {
        return sprintf('%s/organizations/%s/shipments', $this->getApiEndpoint(), $this->organizationId);
    }

    private function getApiEndpointForPointByName(string $name): string
    {
        return sprintf('%s/points/%s', $this->getApiEndpoint(), $name);
    }

    private function getApiEndpointForOrganizations(): string
    {
        return sprintf('%s/organizations', $this->getApiEndpoint());
    }

    private function getApiEndpointForLabels(): string
    {
        return sprintf('%s/organizations/%s/shipments/labels', $this->getApiEndpoint(), $this->organizationId);
    }

    private function getApiEndpointForShipmentById(int $id): string
    {
        return sprintf('%s/shipments/%s', $this->getApiEndpoint(), $id);
    }
}
