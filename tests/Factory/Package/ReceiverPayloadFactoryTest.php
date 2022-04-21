<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory\Package;

use BitBag\ShopwareInPostPlugin\Factory\Package\ReceiverPayloadFactory;
use BitBag\ShopwareInPostPlugin\Provider\Defaults;
use BitBag\ShopwareInPostPlugin\Resolver\OrderShippingAddressResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Order\OrderEntity;

final class ReceiverPayloadFactoryTest extends TestCase
{
    use CreateOrderShippingAddressTrait;

    public function testCreate(): void
    {
        $orderShippingAddress = $this->getOrderShippingAddress();

        $orderShippingAddressResolver = $this->createMock(OrderShippingAddressResolverInterface::class);

        $orderShippingAddressResolver->expects(self::once())
                                     ->method('resolve')
                                     ->willReturn($orderShippingAddress);

        $receiverPayloadFactory = new ReceiverPayloadFactory($orderShippingAddressResolver);

        self::assertEquals(
            [
                'company_name' => $orderShippingAddress->getCompany(),
                'first_name' => $orderShippingAddress->getFirstName(),
                'last_name' => $orderShippingAddress->getLastName(),
                'email' => 'email@website.com',
                'phone' => '123456789',
                'address' => [
                    'street' => 'Polna',
                    'building_number' => '11',
                    'city' => $orderShippingAddress->getCity(),
                    'post_code' => $orderShippingAddress->getZipcode(),
                    'country_code' => Defaults::CURRENCY_CODE,
                ],
            ],
            $receiverPayloadFactory->create($this->getOrder())
        );
    }

    private function getOrder(): OrderEntity
    {
        $orderCustomer = new OrderCustomerEntity();
        $orderCustomer->setEmail('email@website.com');

        $order = new OrderEntity();
        $order->setOrderCustomer($orderCustomer);

        return $order;
    }
}
