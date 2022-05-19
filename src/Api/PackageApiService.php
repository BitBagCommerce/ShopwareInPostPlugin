<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Exception\InPostApiException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Exception\PackageNotFoundException;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactoryInterface;
use GuzzleHttp\Exception\ClientException;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class PackageApiService implements PackageApiServiceInterface
{
    private PackagePayloadFactoryInterface $packagePayloadFactory;

    private SalesChannelAwareWebClientInterface $salesChannelAwareWebClient;

    public function __construct(
        PackagePayloadFactoryInterface $packagePayloadFactory,
        SalesChannelAwareWebClientInterface $salesChannelAwareWebClient
    ) {
        $this->packagePayloadFactory = $packagePayloadFactory;
        $this->salesChannelAwareWebClient = $salesChannelAwareWebClient;
    }

    /** @psalm-return array<array-key, mixed> */
    public function createPackage(OrderEntity $order, Context $context): array
    {
        $inPostPackageData = $this->packagePayloadFactory->create($order, $context);

        try {
            $package = $this->salesChannelAwareWebClient->createShipment($inPostPackageData, $order->getSalesChannelId());
        } catch (ClientException $e) {
            $error = json_decode($e->getMessage(), true);
            $errorDetails = $error['details'];

            if ([] !== $errorDetails) {
                if (isset($errorDetails['custom_attributes'][0]['target_point']) &&
                    'does_not_exist' === $errorDetails['custom_attributes'][0]['target_point'][0]
                ) {
                    throw new PackageNotFoundException('package.pointNameNotFound');
                }

                if (isset($errorDetails['receiver'][0]['email']) &&
                    'invalid' === $errorDetails['receiver'][0]['email'][0]
                ) {
                    throw new PackageException('package.emailInvalid');
                }

                if (isset($errorDetails['receiver'][0]['phone'][0]) &&
                    'invalid' === $errorDetails['receiver'][0]['phone'][0]
                ) {
                    throw new PackageException('package.phoneNumberInvalid');
                }
            }

            if (isset($error['error']) && 'token_invalid' === $error['error']) {
                throw new InPostApiException('api.providedDataNotValid');
            }

            throw $e;
        }

        if (!isset($package['error'])) {
            return $package;
        }

        switch ($package['error']) {
            case 'validation_failed':
                throw new InPostApiException('api.providedDataNotValid');
            case 'resource_not_found':
                throw new InPostApiException('api.resourceNotFound');
            case 'no_carriers':
                throw new InPostApiException('api.noCarriers');
            default:
                return $package;
        }
    }
}
