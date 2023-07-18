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
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Service\OrderServiceInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class OrderCourierController
{
    private OrderFinderInterface $orderFinder;

    private SalesChannelAwareWebClientInterface $salesChannelAwareWebClient;

    private OrderServiceInterface $orderService;

    public function __construct(
        OrderFinderInterface $orderFinder,
        SalesChannelAwareWebClientInterface $salesChannelAwareWebClient,
        OrderServiceInterface $orderService
    ) {
        $this->orderFinder = $orderFinder;
        $this->salesChannelAwareWebClient = $salesChannelAwareWebClient;
        $this->orderService = $orderService;
    }

    /**
     * @OA\Post(
     *     path="/api/_action/bitbag-inpost-plugin/order-courier",
     *     summary="Order an courier to get packages from selected orders",
     *     operationId="orderCourier",
     *     tags={"Admin API", "InPost"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 description="Name (e.g. company name)",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="phoneNumber",
     *                 description="Phone number",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="E-mail",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="street",
     *                 description="Street",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="buildingNumber",
     *                 description="Building number",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 description="City",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="postalCode",
     *                 description="Postal code",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Courier was ordered."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad data provided"
     *     )
     * )
     * @Route("/api/_action/bitbag-inpost-plugin/order-courier", name="api.action.bitbag_inpost_plugin.order_courier", methods={"POST"})
     */
    public function orderCourier(Request $request, Context $context): JsonResponse
    {
        $data = $request->toArray();
        $ordersIds = $data['ordersIds'];
        $formValues = $data['formValues'];

        if ([] === $ordersIds) {
            return new JsonResponse(['error' => true, 'message' => 'order.notFound']);
        }

        if (false === $this->isValidFormValues($formValues)) {
            return new JsonResponse(['error' => true, 'message' => 'popup.providedDataNotValid']);
        }

        $packagesIds = $this->getPackageIds($ordersIds, $context);
        $ordersIdsInPostOnly = $this->getOrderIds($ordersIds, $context);

        if ([] === $packagesIds || [] === $ordersIdsInPostOnly) {
            return new JsonResponse(['error' => true, 'message' => 'popup.notFoundOrderForCourierOrCourierOrdered'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'shipments' => $packagesIds,
            'name' => $formValues['name'],
            'phone' => str_replace(['-', ' ', '+48 ', '+48'], '', $formValues['phoneNumber']),
            'email' => $formValues['email'],
            'address' => [
                'street' => $formValues['street'],
                'building_number' => $formValues['buildingNumber'],
                'city' => $formValues['city'],
                'post_code' => $formValues['postCode'],
                'country_code' => Defaults::CURRENCY_CODE,
            ],
        ];

        $result = $this->salesChannelAwareWebClient->orderCourier($data);

        if (!isset($result['shipments'])) {
            return new JsonResponse(['error' => true, 'message' => 'popup.problemWhileOrderCourier']);
        }

        $ordersInPostOnly = $this->orderFinder->getWithAssociationsByOrdersIds($ordersIdsInPostOnly, $context);

        $this->orderService->saveTrackingNumberInOrder(
            $result['shipments'],
            $ordersInPostOnly->getElements(),
            $context
        );

        return new JsonResponse(['error' => false, 'message' => 'popup.courierOrdered']);
    }

    private function isValidFormValues(array $formValues): bool
    {
        return isset(
                $formValues['name'],
                $formValues['phoneNumber'],
                $formValues['email'],
                $formValues['street'],
                $formValues['buildingNumber'],
                $formValues['city'],
                $formValues['postCode']
            ) && (
                '' !== $formValues['name'] &&
                '' !== $formValues['phoneNumber'] &&
                '' !== $formValues['email'] &&
                '' !== $formValues['street'] &&
                '' !== $formValues['buildingNumber'] &&
                '' !== $formValues['city'] &&
                '' !== $formValues['postCode']
            );
    }

    private function getOrders(array $ordersIds, Context $context): array
    {
        $orders = $this->orderFinder->getWithAssociationsByOrdersIds($ordersIds, $context);

        $ordersInPostOnly = [];

        /** @var OrderEntity $order */
        foreach ($orders->getElements() as $order) {
            $deliveries = $order->getDeliveries();

            if (null === $deliveries) {
                continue;
            }

            $delivery = $deliveries->first();

            if (null === $delivery) {
                continue;
            }

            $shippingMethod = $delivery->getShippingMethod();

            if (null === $shippingMethod) {
                continue;
            }

            $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;

            /** @var ArrayEntity|null $inPostExtension */
            $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

            if (null !== $technicalName &&
                ShippingMethodPayloadFactoryInterface::SHIPPING_KEY === $technicalName &&
                null !== $inPostExtension &&
                null !== $inPostExtension->get('packageId') &&
                WebClientInterface::SENDING_METHOD_PARCEL_LOCKER !== $inPostExtension->get('sendingMethod') &&
                0 === count($delivery->getTrackingCodes())
            ) {
                $ordersInPostOnly[] = $order;
            }
        }

        return $ordersInPostOnly;
    }

    private function getOrderIds(array $ordersIds, Context $context): array
    {
        $ordersInPostOnly = $this->getOrders($ordersIds, $context);
        $orderIdsInPostOnly = [];

        /** @var OrderEntity $order */
        foreach ($ordersInPostOnly as $order) {
            $orderIdsInPostOnly[] = $order->getId();
        }

        return $orderIdsInPostOnly;
    }

    private function getPackageIds(array $ordersIds, Context $context): array
    {
        $ordersInPostOnly = $this->getOrders($ordersIds, $context);
        $packagesIds = [];

        /** @var OrderEntity $order */
        foreach ($ordersInPostOnly as $order) {
            /** @var ArrayEntity|null $inPostExtension */
            $inPostExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);
            if (null !== $inPostExtension) {
                $packagesIds[] = $inPostExtension->get('packageId');
            }
        }

        return $packagesIds;
    }
}
