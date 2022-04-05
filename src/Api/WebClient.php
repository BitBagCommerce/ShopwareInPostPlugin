<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Resolver\ApiDataResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

final class WebClient implements WebClientInterface
{
    private Client $apiClient;

    private ?string $organizationId;

    private ?string $accessToken;

    private ?string $environment;

    public function __construct(Client $client, ApiDataResolver $apiDataResolver)
    {
        $this->apiClient = $client;
        $this->accessToken = $apiDataResolver->getAccessToken();
        $this->organizationId = $apiDataResolver->getOrganizationId();
        $this->environment = $apiDataResolver->getEnvironment();
    }

    public function getApiEndpoint(): string
    {
        $apiEndpoint = self::SANDBOX_ENVIRONMENT === $this->environment ? self::SANDBOX_API_ENDPOINT : self::PRODUCTION_API_ENDPOINT;

        return sprintf('%s/%s', $apiEndpoint, self::API_VERSION);
    }

    public function getApiEndpointForShipment(): string
    {
        return sprintf('%s/organizations/%s/shipments', $this->getApiEndpoint(), $this->organizationId);
    }

    public function getApiEndpointForPointByName(string $name): string
    {
        return sprintf('%s/points/%s', $this->getApiEndpoint(), $name);
    }

    public function getApiEndpointForOrganizations(): string
    {
        return sprintf('%s/organizations', $this->getApiEndpoint());
    }

    public function getPointByName(string $name, int $attempts = 0): ?array
    {
        $url = $this->getApiEndpointForPointByName($name);

        try {
            return $this->request('GET', $url);
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

        return $this->request('GET', $url);
    }

    public function getApiEndpointForLabels(): string
    {
        return sprintf('%s/organizations/%s/shipments/labels', $this->getApiEndpoint(), $this->organizationId);
    }

    public function getApiEndpointForShipmentById(int $id): string
    {
        return sprintf('%s/shipments/%s', $this->getApiEndpoint(), $id);
    }

    public function getShipmentById(int $id): ?array
    {
        $url = $this->getApiEndpointForShipmentById($id);

        return $this->request('GET', $url);
    }

    public function getLabels(array $shipmentIds): ?string
    {
        $url = $this->getApiEndpointForLabels();

        $data = [
            'format' => 'pdf',
            'shipment_ids' => $shipmentIds,
        ];

        return $this->request('POST', $url, $data, false);
    }

    public function getShipments(): ?array
    {
        $url = $this->getApiEndpointForShipment();

        return $this->request('GET', $url);
    }

    public function getAuthorizedHeaderWithContentType(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->accessToken),
        ];
    }

    /**
     * @return string|array
     */
    public function request(string $method, string $url, array $data = [], bool $returnJson = true)
    {
        $options = [
            'json' => $data,
            'headers' => $this->getAuthorizedHeaderWithContentType(),
        ];

        try {
            $result = $this->apiClient->request($method, $url, $options);
        } catch (ClientException $exception) {
            /** @var ?ResponseInterface $result */
            $result = $exception->getResponse();

            throw new ClientException(
                null !== $result ? (string) $result->getBody() : 'Request failed for url' . $url,
                $exception->getRequest(),
                $result
            );
        }

        if (false === $returnJson) {
            return (string) $result->getBody();
        }

        return Utils::jsonDecode((string) $result->getBody(), true);
    }

    public function getLabelByShipmentId(string $shipmentId): Response
    {
        $url = $this->getApiEndpoint() . "/shipments/${shipmentId}/label";
        $label = $this->request('GET', $url, [], false);

        $filename = sprintf('filename="label_%s.pdf"', $shipmentId);

        $response = new Response($label);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
