<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use GuzzleHttp\Exception\ClientException;

final class TestWebClient implements TestWebClientInterface
{
    private WebClientInterface $webClient;

    public function __construct(WebClientInterface $webClient)
    {
        $this->webClient = $webClient;
    }

    public function checkCredentials(string $accessToken, string $organizationId, string $environment): bool
    {
        try {
            $apiBaseUrl = $this->webClient->getApiBaseUrl(
                WebClientInterface::SANDBOX_ENVIRONMENT === $environment
            );

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $accessToken),
            ];

            $this->webClient->request(
                'GET',
                $apiBaseUrl . "/organizations/${organizationId}/dispatch_orders",
                $headers
            );

            return true;
        } catch (ClientException $e) {
            return false;
        }
    }
}
