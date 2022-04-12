<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\PackageApiServiceInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Validator\InpostShippingMethodValidatorInterface;
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
     * @Route("/api/_action/bitbag-inpost-plugin/create-package/{orderId}", name="api.action.bitbag_inpost_plugin.create_package", methods={"POST"})
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
