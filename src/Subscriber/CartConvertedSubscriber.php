<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Subscriber;

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
            CartConvertedEvent::class => 'addPointNameToInpostExtension',
        ];
    }

    public function addPointNameToInpostExtension(CartConvertedEvent $event): void
    {
        $orderData = $event->getConvertedCart();

        $criteria = (new Criteria())->addFilter(new EqualsFilter('name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        $shippingMethodTranslations = $this->shippingMethodTranslationRepository->search($criteria, $event->getContext());

        if (0 === $shippingMethodTranslations->count()) {
            return;
        }

        /** @var ShippingMethodTranslationEntity $shippingMethodTranslation */
        $shippingMethodTranslation = $shippingMethodTranslations->first();

        if ($orderData['deliveries'][0]['shippingMethodId'] !== $shippingMethodTranslation->getShippingMethodId()) {
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

        $orderData['extensions']['inPost'] = [
            'id' => Uuid::randomHex(),
            'pointName' => $customParcelLocker,
        ];

        $event->setConvertedCart($orderData);
    }
}
