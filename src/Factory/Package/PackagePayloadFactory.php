<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Config\InPostConfigServiceInterface;
use BitBag\ShopwareInPostPlugin\Exception\PackageNotFoundException;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderCustomFieldsResolverInterface;
use BitBag\ShopwareInPostPlugin\Resolver\OrderExtensionDataResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class PackagePayloadFactory implements PackagePayloadFactoryInterface
{
    private ReceiverPayloadFactoryInterface $createReceiverPayloadFactory;

    private ParcelPayloadFactoryInterface $createParcelPayloadFactory;

    private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver;

    private OrderExtensionDataResolverInterface $orderExtensionDataResolver;

    private InPostConfigServiceInterface $inPostConfigService;

    public function __construct(
        ReceiverPayloadFactoryInterface $createReceiverPayloadFactory,
        ParcelPayloadFactoryInterface $parcelPayloadFactory,
        OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        OrderExtensionDataResolverInterface $orderExtensionDataResolver,
        InPostConfigServiceInterface $inPostConfigService
    ) {
        $this->createReceiverPayloadFactory = $createReceiverPayloadFactory;
        $this->createParcelPayloadFactory = $parcelPayloadFactory;
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->orderExtensionDataResolver = $orderExtensionDataResolver;
        $this->inPostConfigService = $inPostConfigService;
    }

    public function create(
        OrderEntity $order,
        Context $context,
        ?string $salesChannelId = null
    ): array {
        $orderInPostExtensionData = $this->orderExtensionDataResolver->resolve($order);

        if (!isset($orderInPostExtensionData['pointName'])) {
            throw new PackageNotFoundException('package.pointNameNotFound');
        }

        $sendingMethod = $this->inPostConfigService->getInPostApiConfig($salesChannelId)->getSendingMethod();

        $data = [
            'receiver' => $this->createReceiverPayloadFactory->create($order),
            'parcels' => [
                $this->createParcelPayloadFactory->create($order, $context),
            ],
            'service' => WebClientInterface::IN_POST_LOCKER_STANDARD_SERVICE,
            'custom_attributes' => [
                'target_point' => $orderInPostExtensionData['pointName'],
            ],
        ];

        if (WebClientInterface::SENDING_METHOD_DISPATCH_ORDER == $sendingMethod) {
            $data['custom_attributes'] = [
                'target_point' => $orderInPostExtensionData['pointName'],
                'sending_method' => WebClientInterface::SENDING_METHOD_DISPATCH_ORDER,
            ];
        } elseif (WebClientInterface::SENDING_METHOD_PARCEL_LOCKER == $sendingMethod) {
            $data['custom_attributes'] = [
                'target_point' => $orderInPostExtensionData['pointName'],
                'sending_method' => WebClientInterface::SENDING_METHOD_PARCEL_LOCKER,
            ];
        }

        $data = $this->addInsurance($data, $order);

        return $data;
    }

    private function addInsurance(array $data, OrderEntity $order): array
    {
        $customFieldInsurance = $this->orderCustomFieldsResolver->resolve($order)['insurance'];

        if (null !== $customFieldInsurance) {
            $data['insurance'] = [
                'amount' => $customFieldInsurance,
                'currency' => Defaults::CURRENCY,
            ];
        }

        return $data;
    }
}
