<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\TestWebClientInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class InPostApiController
{
    private TestWebClientInterface $testWebClient;

    public function __construct(TestWebClientInterface $testWebClient)
    {
        $this->testWebClient = $testWebClient;
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
     *         response="403",
     *         description="InPost data are not valid"
     *     )
     * )
     * @Route("/api/_action/bitbag-inpost-plugin/check-credentials", name="api.action.bitbag_inpost_plugin.check_credentials", methods={"POST"})
     */
    public function checkCredentials(Request $request): JsonResponse
    {
        /** @var array $data = ["accessToken" => "", "organizationId" => "", "environment" => ""] */
        $data = $request->toArray();

        $accessToken = $data['accessToken'] ?? null;
        $organizationId = $data['organizationId'] ?? null;
        $environment = $data['environment'] ?? null;

        if ((null === $accessToken || '' === $accessToken) ||
            (null === $organizationId || '' === $organizationId) ||
            (null === $environment || '' === $environment)
        ) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $result = $this->testWebClient->checkCredentials(
            $data['accessToken'],
            $organizationId,
            $data['environment']
        );

        return new JsonResponse([], $result ? Response::HTTP_OK : Response::HTTP_FORBIDDEN);
    }
}
