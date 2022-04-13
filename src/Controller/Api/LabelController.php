<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Controller\Api;

use BitBag\ShopwareInPostPlugin\Api\WebClientInterface;
use BitBag\ShopwareInPostPlugin\Exception\OrderException;
use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Extension\Content\Order\OrderInPostExtensionInterface;
use BitBag\ShopwareInPostPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareInPostPlugin\Validator\InpostShippingMethodValidatorInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
final class LabelController
{
    private OrderFinderInterface $orderFinder;

    private WebClientInterface $webClient;

    private InpostShippingMethodValidatorInterface $inpostShippingMethodValidator;

    public function __construct(
        OrderFinderInterface $orderFinder,
        WebClientInterface $webClient,
        InpostShippingMethodValidatorInterface $inpostShippingMethodValidator
    ) {
        $this->orderFinder = $orderFinder;
        $this->webClient = $webClient;
        $this->inpostShippingMethodValidator = $inpostShippingMethodValidator;
    }

    /**
     * @Route("/api/_action/bitbag-inpost-plugin/get-label/{orderId}", name="api.action.bitbag_inpost_plugin.get_label", methods={"GET"})
     */
    public function getLabel(string $orderId, Context $context): Response
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $this->inpostShippingMethodValidator->validate($order);

        $orderExtension = $order->getExtension(OrderInPostExtensionInterface::PROPERTY_KEY);

        if (null === $orderExtension) {
            throw new OrderException('order.extension.notFoundInPost');
        }

        /** @var array $orderInPostExtensionData = ['pointName' => 'string', 'packageId' => 'integer'] */
        $orderInPostExtensionData = $orderExtension->getVars()['data'];

        if (null === $orderInPostExtensionData['packageId']) {
            throw new PackageException('package.packageIdNotFound');
        }

        return $this->webClient->getLabelByShipmentId($orderInPostExtensionData['packageId']);
    }
}
