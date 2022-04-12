<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Validator;

use BitBag\ShopwareInPostPlugin\Exception\PackageException;
use BitBag\ShopwareInPostPlugin\Resolver\ApiDataResolverInterface;
use Shopware\Core\Framework\Context;

final class ApiDataValidator implements ApiDataValidatorInterface
{
    private ApiDataResolverInterface $apiDataResolver;

    public function __construct(ApiDataResolverInterface $apiDataResolver)
    {
        $this->apiDataResolver = $apiDataResolver;
    }

    public function validate(Context $context): bool
    {
        $apiDataResolver = $this->apiDataResolver;

        if (null === $apiDataResolver->getEnvironment() ||
            null == $apiDataResolver->getOrganizationId() ||
            null === $apiDataResolver->getAccessToken()
        ) {
            throw new PackageException('package.apiDataNotFound');
        }

        return true;
    }
}
