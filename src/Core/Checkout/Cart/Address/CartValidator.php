<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Address;

use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error\InvalidPostCodeError;
use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error\NullWeightError;
use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Custom\Error\StreetSplittingError;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\Delivery;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

final class CartValidator implements CartValidatorInterface
{
    public const STREET_FIRST_REGEX = "/(?<streetName>[[:alnum:].'\- ]+)\s+(?<houseNumber>\d{1,10}((\s)?\w{1,3})?(\/\d{1,10})?)$/";

    public const STREET_WITH_BUILDING_NUMBER_REGEX = "/^([^\d]*[^\d\s]) *(\d.*)$/";

    public const POST_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

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

        preg_match(self::STREET_FIRST_REGEX, $address->getStreet(), $streetMatches);

        if (empty($streetMatches)) {
            $errors->add(new StreetSplittingError($address->getId()));

            return;
        }

        $shippingMethodCustomFields = $context->getShippingMethod()->getCustomFields();

        if (!isset($shippingMethodCustomFields['technical_name']) ||
            $shippingMethodCustomFields['technical_name'] !== ShippingMethodPayloadFactoryInterface::SHIPPING_KEY
        ) {
            return;
        }

        $postCode = $address->getZipcode();

        $this->checkPostCodeValidity($postCode, $address->getId(), $errors);

        if (!preg_match(self::STREET_WITH_BUILDING_NUMBER_REGEX, $address->getStreet())) {
            $errors->add(new StreetSplittingError($address->getId()));

            return;
        }

        /** @var LineItem $lineItem */
        foreach ($cart->getLineItems()->getElements() as $lineItem) {
            $deliveryInformation = $lineItem->getDeliveryInformation();
            if (null !== $deliveryInformation) {
                if (0.0 === $deliveryInformation->getWeight()) {
                    $errors->add(new NullWeightError($cart->getToken()));

                    return;
                }
            }
        }
    }

    private function isPostCodeValid(string $postCode): bool
    {
        return (bool) preg_match(self::POST_CODE_REGEX, $postCode);
    }

    private function checkPostCodeValidity(string $postCode, string $addressId, ErrorCollection $errors): void
    {
        if (!$this->isPostCodeValid($postCode)) {
            $postCode = trim(substr_replace($postCode, '-', 2, 0));

            if (!$this->isPostCodeValid($postCode)) {
                $errors->add(new InvalidPostCodeError($addressId));
            }
        }
    }
}