<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Validator\CartValidator;
use BitBag\ShopwareInPostPlugin\Exception\Order\OrderException;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderShippingAddressResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class ReceiverPayloadFactory implements ReceiverPayloadFactoryInterface
{
    private OrderShippingAddressResolverInterface $orderShippingAddressResolver;

    public function __construct(OrderShippingAddressResolverInterface $orderShippingAddressResolver)
    {
        $this->orderShippingAddressResolver = $orderShippingAddressResolver;
    }

    public function create(OrderEntity $order): array
    {
        $orderCustomer = $order->getOrderCustomer();

        if (null === $orderCustomer) {
            throw new OrderException('order.customerEmailNotFound');
        }

        $orderShippingAddress = $this->orderShippingAddressResolver->resolve($order);

        $phoneNumber = $orderShippingAddress->getPhoneNumber();

        if (null === $phoneNumber) {
            throw new OrderException('order.nullPhoneNumber');
        }

        [, $street, $houseNumber] = $this->splitStreet($orderShippingAddress->getStreet());

        return [
            'company_name' => $orderShippingAddress->getCompany(),
            'first_name' => $orderShippingAddress->getFirstName(),
            'last_name' => $orderShippingAddress->getLastName(),
            'email' => $orderCustomer->getEmail(),
            'phone' => $this->sanitizePhoneNumber($phoneNumber),
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
        preg_match(CartValidator::STREET_FIRST_REGEX, $street, $streetAddress);

        return $streetAddress;
    }

    private function sanitizePhoneNumber(string $value): string
    {
        return str_replace(['-', ' ', '+48 ', '+48'], '', $value);
    }
}
