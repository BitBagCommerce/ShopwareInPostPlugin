<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
}
