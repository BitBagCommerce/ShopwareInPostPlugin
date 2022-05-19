<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\SalesChannelAwareWebClientInterface;
use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use GuzzleHttp\Exception\ClientException;
use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class InPostApiController
{
    private WebClientInterface $webClient;

    private SalesChannelAwareWebClientInterface $salesChannelAwareWebClient;

    public function __construct(WebClientInterface $webClient, SalesChannelAwareWebClientInterface $salesChannelAwareWebClient)
    {
        $this->webClient = $webClient;
        $this->salesChannelAwareWebClient = $salesChannelAwareWebClient;
    }

    /**
     * @OA\Post(
     *     path="/api/_action/bitbag-inpost-plugin/check-credentials",
     *     summary="Chceck that InPost data in config are correct",
     *     operationId="checkCredentials",
     *     tags={"Admin API", "InPost"},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="salesChannelId",
     *                 description="Sales channel id",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="accessToken",
     *                 description="InPost API access token",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="environment",
     *                 description="InPost API environment",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="InPost data are valid",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="InPost data are not valid"
     *     )
     * )
     * @Route("/api/_action/bitbag-inpost-plugin/check-credentials", name="api.action.bitbag_inpost_plugin.check_credentials", methods={"POST"})
     */
    public function checkCredentials(Request $request): JsonResponse
    {
        /** @var array $data = ["accessToken" => "", "organizationId" => "", "environment" => ""] */
        $data = $request->toArray();

        try {
            $apiBaseUrl = $this->webClient->getApiBaseUrl(
                WebClientInterface::SANDBOX_ENVIRONMENT === $data['environment']
            );

            $organizationId = $data['organizationId'];

            if (null === $organizationId || '' === $organizationId) {
                return new JsonResponse(['success' => false], Response::HTTP_FORBIDDEN);
            }

            $this->webClient->request(
                'GET',
                $apiBaseUrl . "/organizations/${organizationId}/dispatch_orders",
                $this->salesChannelAwareWebClient->getHeaders($data['accessToken'])
            );

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (ClientException $e) {
            return new JsonResponse(['success' => false], Response::HTTP_FORBIDDEN);
        }
    }
}
