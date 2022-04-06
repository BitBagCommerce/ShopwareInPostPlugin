<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Resolver\ApiDataResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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
        if (!$this->organizationId) {
            throw new \Exception('Organization id was not found');
        }

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

    /**
     * @psalm-return array<array-key, mixed|null>|null|string
     */
    public function getPointByName(string $name, int $attempts = 0)
    {
        $url = $this->getApiEndpointForPointByName($name);

        try {
            $request = $this->request('GET', $url);

            return \json_decode($request, true);
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

        return \json_decode($request, true);
    }

    public function getApiEndpointForLabels(): string
    {
        if (!$this->organizationId) {
            throw new \Exception('Organization id was not found');
        }

        return sprintf('%s/organizations/%s/shipments/labels', $this->getApiEndpoint(), $this->organizationId);
    }

    public function getApiEndpointForShipmentById(int $id): string
    {
        return sprintf('%s/shipments/%s', $this->getApiEndpoint(), $id);
    }

    public function getShipmentById(int $id): ?array
    {
        $url = $this->getApiEndpointForShipmentById($id);

        $request = $this->request('GET', $url);

        return \json_decode($request, true);
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

        return \json_decode($request, true);
    }

    public function getAuthorizedHeaderWithContentType(): array
    {
        if (!$this->accessToken) {
            throw new \Exception('Access token was not found');
        }

        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->accessToken),
        ];
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

    public function getLabelByShipmentId(string $shipmentId): Response
    {
        $url = $this->getApiEndpoint() . "/shipments/${shipmentId}/label";
        $label = $this->request('GET', $url, []);

        $filename = sprintf('filename="label_%s.pdf"', $shipmentId);

        $response = new Response($label);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
