<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory\Package;

use BitBag\ShopwareInPostPlugin\Exception\Order\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\ShippingAddress\ShippingAddressException;
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
            'phone' => $this->replaceString($phoneNumber),
            'address' => [
                'street' => $street,
                'building_number' => $houseNumber,
                'city' => $orderShippingAddress->getCity(),
                'post_code' => $this->getValidPostCode($orderShippingAddress->getZipcode()),
                'country_code' => Defaults::CURRENCY_CODE,
            ],
        ];
    }

    private function splitStreet(string $street): array
    {
        if (!preg_match('/^([^\d]*[^\d\s]) *(\d.*)$/', $street, $streetAddress)) {
            throw new ShippingAddressException('Street cannot be split');
        }

        return $streetAddress;
    }

    private function replaceString(string $value): string
    {
        return str_replace(['-', ' '], '', $value);
    }

    private function isValidPostCode(string $postCode): bool
    {
        return (bool) preg_match('/^(\d{2})(-\d{3})?$/i', $postCode);
    }

    private function getValidPostCode(string $postCode): string
    {
        return $this->isValidPostCode($postCode) ?
            $postCode :
            trim(substr_replace($postCode, '-', 2, 0));
    }
}
