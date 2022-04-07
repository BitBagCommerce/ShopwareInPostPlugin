<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

final class CreateReceiverPackagePayloadFactory implements CreateReceiverPackagePayloadFactoryInterface
{
    public function create(OrderEntity $order): array
    {
        if ((null === $orderDelivery = $order->getDeliveries()) ||
            (null === $firstOrderDelivery = $orderDelivery->first())
        ) {
            throw new \Exception('error.wentWrong');
        }

        if (null === $orderCustomer = $order->getOrderCustomer()) {
            throw new \Exception('error.wentWrong');
        }

        /** @var OrderAddressEntity $orderShippingAddress */
        $orderShippingAddress = $firstOrderDelivery->getShippingOrderAddress();

        if (null === $phoneNumber = $orderShippingAddress->getPhoneNumber()) {
            throw new \Exception('order.nullPhoneNumber');
        }

        $street = $orderShippingAddress->getStreet();

        [, $street, $houseNumber] = $this->splitStreet($street);

        return [
            'company_name' => $orderShippingAddress->getCompany(),
            'first_name' => $orderShippingAddress->getFirstName(),
            'last_name' => $orderShippingAddress->getLastName(),
            'email' => $orderCustomer->getEmail(),
            'phone' => str_replace(['-', ' '], '', $phoneNumber),
            'address' => [
                'street' => $street,
                'building_number' => $houseNumber,
                'city' => $orderShippingAddress->getCity(),
                'post_code' => $orderShippingAddress->getZipcode(),
                'country_code' => Defaults::CURRENCY_CODE,
            ],
        ];
    }

    private function splitStreet(string $street): array
    {
        if (!preg_match('/^([^\d]*[^\d\s]) *(\d.*)$/', $street, $streetAddress)) {
            throw new \Exception('Street cannot be split');
        }

        return $streetAddress;
    }
}
