<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Subscriber;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CheckoutFinishSubscriber implements EventSubscriberInterface
{
    private WebClientInterface $webClient;

    public function __construct(WebClientInterface $webClient)
    {
        $this->webClient = $webClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutFinishPageLoadedEvent::class => 'checkoutFinishPageLoaded',
        ];
    }

    public function checkoutFinishPageLoaded(CheckoutFinishPageLoadedEvent $event): void
    {
        $shippingMethod = $event->getSalesChannelContext()->getShippingMethod()->getName();

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY === $shippingMethod) {
            $pointDetails = $this->fetchPoint($event);

            /**
             * @psalm-suppress DeprecatedMethod
             */
            $event->getPage()->addExtension('point_details', new ArrayStruct($pointDetails));
        }
    }

    public function fetchPoint(CheckoutFinishPageLoadedEvent $event): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        /**
         * @psalm-suppress UndefinedMethod
         */
        $point = $event->getPage()->getOrder()->getExtensions()['inPost']['pointName'];
        $url = WebClientInterface::IN_POST_API_POINTS_ENDPOINT . $point;

        $pointDetailsData = $this->webClient->request(
            'GET',
            $url,
            $headers
        );

        return json_decode($pointDetailsData, true);
    }
}
