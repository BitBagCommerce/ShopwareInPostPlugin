<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Subscriber;

use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Validator\CartValidator;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartConvertedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private EntityRepositoryInterface $shippingMethodTranslationRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityRepositoryInterface $shippingMethodTranslationRepository
    ) {
        $this->requestStack = $requestStack;
        $this->shippingMethodTranslationRepository = $shippingMethodTranslationRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => 'updateInPostOrderData',
        ];
    }

    public function updateInPostOrderData(CartConvertedEvent $event): void
    {
        $orderData = $event->getConvertedCart();

        $criteria = (new Criteria())->addFilter(new EqualsFilter('customFields.technical_name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        $shippingMethodTranslations = $this->shippingMethodTranslationRepository->search($criteria, $event->getContext());

        if (0 === $shippingMethodTranslations->count()) {
            return;
        }

        /** @var ShippingMethodTranslationEntity $shippingMethodTranslation */
        $shippingMethodTranslation = $shippingMethodTranslations->first();

        $delivery = $orderData['deliveries'][0];

        if ($delivery['shippingMethodId'] !== $shippingMethodTranslation->getShippingMethodId()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $customParcelLocker = $request->request->get('customParcelLockerField', null);

        if (null === $customParcelLocker) {
            return;
        }

        $orderPostCode = $delivery['shippingOrderAddress']['zipcode'];

        if (!$this->isPostCodeValid($delivery['shippingOrderAddress']['zipcode'])) {
            $delivery['shippingOrderAddress']['zipcode'] = trim(substr_replace($orderPostCode, '-', 2, 0));

            $orderData['deliveries'][0] = $delivery;
        }

        $orderData['extensions'][OrderInPostExtensionInterface::PROPERTY_KEY] = [
            'id' => Uuid::randomHex(),
            'pointName' => $customParcelLocker,
        ];

        $event->setConvertedCart($orderData);
    }

    private function isPostCodeValid(string $postCode): bool
    {
        return (bool) preg_match(CartValidator::POST_CODE_REGEX, $postCode);
    }
}
