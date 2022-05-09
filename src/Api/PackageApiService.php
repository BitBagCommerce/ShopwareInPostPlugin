<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Api;

use BitBag\ShopwareInPostPlugin\Exception\InpostApiException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Exception\PackageNotFoundException;
use BitBag\ShopwareInPostPlugin\Factory\Package\PackagePayloadFactoryInterface;
use GuzzleHttp\Exception\ClientException;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

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
    public function createPackage(OrderEntity $order, Context $context): array
    {
        $inPostPackageData = $this->packagePayloadFactory->create($order, $context);

        try {
            $package = $this->webClient->createShipment($inPostPackageData);
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

            throw $e;
        }

        if (!isset($package['error'])) {
            return $package;
        }

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
