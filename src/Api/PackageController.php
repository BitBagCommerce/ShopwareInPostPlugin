<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Validator\ApiDataValidatorInterface;
use BitBag\ShopwareInPostPlugin\Validator\OrderValidatorInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class PackageController
{
    private EntityRepositoryInterface $orderRepository;

    private PackagePayloadFactoryInterface $packagePayloadFactory;

    private OrderValidatorInterface $orderValidator;

    private OrderFinderInterface $orderFinder;

    private WebClientInterface $webClient;

    private ApiDataValidatorInterface $apiDataValidator;

    public function __construct(
        EntityRepositoryInterface $orderRepository,
        PackagePayloadFactoryInterface $packagePayloadFactory,
        OrderValidatorInterface $orderValidator,
        OrderFinderInterface $orderFinder,
        WebClientInterface $webClient,
        ApiDataValidatorInterface $apiDataValidator
    ) {
        $this->orderRepository = $orderRepository;
        $this->packagePayloadFactory = $packagePayloadFactory;
        $this->orderValidator = $orderValidator;
        $this->orderFinder = $orderFinder;
        $this->webClient = $webClient;
        $this->apiDataValidator = $apiDataValidator;
    }

    /**
     * @Route("/api/order/{orderId}/inpost/create-package", name="custom.api.inpost.create_package", methods={"POST"})
     */
    public function create(Context $context, string $orderId): Response
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        if (null === $order) {
            throw new OrderException('order.notFound');
        }

        $this->orderValidator->validate($order, $context);

        $this->apiDataValidator->validate($context);

        try {
            $inPostPackageData = $this->packagePayloadFactory->create($order);

            $package = $this->webClient->createShipment($inPostPackageData);
        } catch (\Exception $e) {
            throw new PackageException($e->getMessage());
        }

        if (!isset($package['id'])) {
            throw new PackageException('package.createdPackageError');
        }

        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new OrderException('order.extension.notFoundInPost');
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

        return new Response('package.created', Response::HTTP_CREATED);
    }
}
