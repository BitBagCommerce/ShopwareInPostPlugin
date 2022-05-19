<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

final class WebClient implements WebClientInterface
{
    private Client $apiClient;

    public function __construct(Client $client)
    {
        $this->apiClient = $client;
    }

    public function request(string $method, string $url, array $headers, array $data = []): string
    {
        $options = [
            'json' => $data,
            'headers' => $headers,
        ];

        try {
            $result = $this->apiClient->request($method, $url, $options);
        } catch (ClientException $exception) {
            $result = $exception->getResponse();

            throw new ClientException(
                (string) $result->getBody(),
                $exception->getRequest(),
                $result
            );
        }

        return (string) $result->getBody();
    }

    public function getApiBaseUrl(bool $sandbox): string
    {
        $url = $sandbox ? self::SANDBOX_API_ENDPOINT : self::PRODUCTION_API_ENDPOINT;

        return sprintf('%s/%s', $url, self::API_VERSION);
    }
}
