<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Subscriber;

use BitBag\ShopwareInPostPlugin\Config\InPostConfigServiceInterface;
use BitBag\ShopwareInPostPlugin\Core\Checkout\Cart\Validator\CartValidator;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartConvertedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private EntityRepository $shippingMethodTranslationRepository;

    private InPostConfigServiceInterface $inPostConfigService;

    public function __construct(
        RequestStack $requestStack,
        EntityRepository $shippingMethodTranslationRepository,
        InPostConfigServiceInterface $inPostConfigService
    ) {
        $this->requestStack = $requestStack;
        $this->shippingMethodTranslationRepository = $shippingMethodTranslationRepository;
        $this->inPostConfigService = $inPostConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => 'updateInPostOrderData',
            CheckoutConfirmPageLoadedEvent::class => 'checkoutConfirmPageLoaded',
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

    public function checkoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        $systemConfigPrefix = InPostConfigServiceInterface::SYSTEM_CONFIG_PREFIX;
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        $configService = $this->inPostConfigService->getInPostApiConfig($salesChannelId);

        $event->getPage()->setExtensions([
            $systemConfigPrefix . '.inPostWidgetToken' => $configService->getWidgetToken(),
            $systemConfigPrefix . '.inPostEnvironment' => $configService->getEnvironment(),
        ]);
    }

    private function isPostCodeValid(string $postCode): bool
    {
        return (bool) preg_match(CartValidator::POST_CODE_REGEX, $postCode);
    }
}
