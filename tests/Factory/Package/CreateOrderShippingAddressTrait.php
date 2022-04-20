<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;

trait CreateOrderShippingAddressTrait
{
    public function getOrderShippingAddress(): OrderAddressEntity
    {
        $orderShippingAddress = new OrderAddressEntity();
        $orderShippingAddress->setPhoneNumber('123-456-789');
        $orderShippingAddress->setStreet('Polna 11');
        $orderShippingAddress->setFirstName('Jan');
        $orderShippingAddress->setLastName('Kowalski');
        $orderShippingAddress->setCity('Warszawa');
        $orderShippingAddress->setZipcode('00-001');

        return $orderShippingAddress;
    }
}
