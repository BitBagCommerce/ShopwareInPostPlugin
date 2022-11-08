<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Subscriber;

use BitBag\ShopwareInPostPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use GuzzleHttp\Client;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CheckoutFinishSubscriber implements EventSubscriberInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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
        /**
         * @psalm-suppress UndefinedMethod
         */
        $point = $event->getPage()->getOrder()->getExtensions()['inPost']['pointName'];
        $url = 'https://api-pl-points.easypack24.net/v1/points/' . $point;

        $pointDetailsData = $this->client->request(
            'GET',
            $url
        )->getBody()->getContents();

        $pointDetails = json_decode($pointDetailsData, true);

        return $pointDetails;
    }
}
