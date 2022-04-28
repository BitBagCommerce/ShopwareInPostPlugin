<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Address;

use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error\PostalCodeInvalidError;
use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error\StreetBuildingNumberError;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\Delivery;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

final class CartValidator implements CartValidatorInterface
{
    public const STREET_FIRST_REGEX = "/(?<streetName>[[:alnum:].'\- ]+)\s+(?<houseNumber>\d{1,10}((\s)?\w{1,3})?(\/\d{1,10})?)$/";

    public function validate(Cart $cart, ErrorCollection $errors, SalesChannelContext $context): void
    {
        /** @var Delivery|null $delivery */
        $delivery = $cart->getDeliveries()->first();
        if (null === $delivery) {
            return;
        }

        /** @var CustomerAddressEntity|null $address */
        $address = $delivery->getLocation()->getAddress();
        if (null === $address) {
            return;
        }

        preg_match(self::STREET_FIRST_REGEX, $address->getStreet(), $matchesStreet);

        if (empty($matchesStreet)) {
            $errors->add(new StreetBuildingNumberError($address->getId()));

            return;
        }

        $shippingMethodCustomFields = $context->getShippingMethod()->getCustomFields();

        if (isset($shippingMethodCustomFields['technical_name']) &&
            $shippingMethodCustomFields['technical_name'] === ShippingMethodPayloadFactoryInterface::SHIPPING_KEY
        ) {
            $postCode = $address->getZipcode();

            if (!$this->isValidPostCode($postCode)) {
                $postCode = trim(substr_replace($postCode, '-', 2, 0));

                if (!$this->isValidPostCode($postCode)) {
                    $errors->add(new PostalCodeInvalidError($address->getId()));

                    return;
                }
            }

            if (!preg_match('/^([^\d]*[^\d\s]) *(\d.*)$/', $address->getStreet())) {
                $errors->add(new StreetBuildingNumberError($address->getId()));

                return;
            }
        }
    }

    private function isValidPostCode(string $postCode): bool
    {
        return (bool) preg_match('/^(\d{2})(-\d{3})?$/i', $postCode);
    }
}
