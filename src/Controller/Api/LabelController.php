<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageNotFoundException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Validator\InpostShippingMethodValidatorInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class LabelController
{
    private OrderFinderInterface $orderFinder;

    private WebClientInterface $webClient;

    private InpostShippingMethodValidatorInterface $inpostShippingMethodValidator;

    public function __construct(
        OrderFinderInterface $orderFinder,
        WebClientInterface $webClient,
        InpostShippingMethodValidatorInterface $inpostShippingMethodValidator
    ) {
        $this->orderFinder = $orderFinder;
        $this->webClient = $webClient;
        $this->inpostShippingMethodValidator = $inpostShippingMethodValidator;
    }

    /**
     * @OA\GET(
     *     path="/api/_action/bitbag-inpost-plugin/label/{orderId}",
     *     summary="Gets an InPost package label for an order",
     *     operationId="show",
     *     tags={"Admin API", "InPost"},
     *     @OA\Parameter(
     *         name="orderId",
     *         description="Identifier of the order the package should be generated for",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="InPost package formatted as PDF",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(
     *                type="string",
     *                format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error while rendering the package label"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     )
     * )
     * @Route("/api/_action/bitbag-inpost-plugin/label/{orderId}", name="api.action.bitbag_inpost_plugin.label", methods={"GET"})
     */
    public function show(string $orderId, Context $context): Response
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $this->inpostShippingMethodValidator->validate($order);

        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new OrderException('order.extension.notFoundInPost');
        }

        /** @var array $orderInPostExtensionData = ['pointName' => 'string', 'packageId' => 'integer'] */
        $orderInPostExtensionData = $orderExtension->getVars()['data'];

        if (null === $orderInPostExtensionData['packageId']) {
            throw new PackageNotFoundException('package.packageIdNotFound');
        }

        $packageId = $orderInPostExtensionData['packageId'];

        $label = $this->webClient->getLabelByShipmentId($packageId);

        $filename = sprintf('filename="label_%s.pdf"', $packageId);

        $response = new Response($label);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
