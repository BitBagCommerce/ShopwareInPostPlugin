<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Exception\InpostApiException;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactoryInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class PackageApiService implements PackageApiServiceInterface
{
    private PackagePayloadFactoryInterface $packagePayloadFactory;

    private WebClientInterface $webClient;

    public function __construct(PackagePayloadFactoryInterface $packagePayloadFactory, WebClientInterface $webClient)
    {
        $this->packagePayloadFactory = $packagePayloadFactory;
        $this->webClient = $webClient;
    }

    /** @psalm-return array<array-key, mixed> */
    public function createPackage(OrderEntity $order): array
    {
        $inPostPackageData = $this->packagePayloadFactory->create($order);

        $package = $this->webClient->createShipment($inPostPackageData);

        switch ($package['error']) {
            case 'validation_failed':
                throw new InpostApiException('api.providedDataNotValid');
            case 'resource_not_found':
                throw new InpostApiException('api.resourceNotFound');
            case 'no_carriers':
                throw new InpostApiException('api.noCarriers');
            default:
                return $package;
        }
    }
}
