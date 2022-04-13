<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\PackageApiServiceInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Validator\InpostShippingMethodValidatorInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class PackageController
{
    private EntityRepositoryInterface $orderRepository;

    private OrderFinderInterface $orderFinder;

    private PackageApiServiceInterface $packageApiService;

    private InpostShippingMethodValidatorInterface $inpostShippingMethodValidator;

    public function __construct(
        EntityRepositoryInterface $orderRepository,
        OrderFinderInterface $orderFinder,
        PackageApiServiceInterface $packageApiService,
        InpostShippingMethodValidatorInterface $inpostShippingMethodValidator
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFinder = $orderFinder;
        $this->packageApiService = $packageApiService;
        $this->inpostShippingMethodValidator = $inpostShippingMethodValidator;
    }

    /**
     * @OA\Post(
     *     path="/api/_action/bitbag-inpost-plugin/package/{orderId}",
     *     summary="Creates an InPost package for an order",
     *     operationId="create",
     *     tags={"Admin API", "InPost"},
     *     @OA\Parameter(
     *         name="orderId",
     *         description="Identifier of the order the package should be generated for",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Package created successfully.",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error while create InPost package"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     )
     * )
     * @Route("/api/_action/bitbag-inpost-plugin/package/{orderId}", name="api.action.bitbag_inpost_plugin.package", methods={"POST"})
     */
    public function create(string $orderId, Context $context): JsonResponse
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $this->inpostShippingMethodValidator->validate($order);

        $package = $this->packageApiService->createPackage($order);

        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new OrderException('order.extension.notFoundInPost');
        }

        /** @var array $orderInPostExtensionData = ['pointName' => 'string', 'packageId' => 'integer'] */
        $orderInPostExtensionData = $orderExtension->getVars()['data'];

        if (null !== $orderInPostExtensionData['packageId']) {
            throw new PackageException('package.alreadyCreated');
        }

        $this->orderRepository->update([
            [
                'id' => $order->getId(),
                'inPost' => [
                    'id' => $orderExtension->getVars()['data']['id'],
                    'packageId' => $package['id'],
                ],
            ],
        ], $context);

        return new JsonResponse('package.created', Response::HTTP_CREATED);
    }
}
