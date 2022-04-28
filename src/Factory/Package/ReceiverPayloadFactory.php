<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Address\CartValidator;
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
            throw new OrderException('order.customerEmailNotFound', $order->getId());
        }

        $orderShippingAddress = $this->orderShippingAddressResolver->resolve($order);

        $phoneNumber = $orderShippingAddress->getPhoneNumber();

        if (null === $phoneNumber) {
            throw new OrderException('order.nullPhoneNumber', $order->getId());
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
