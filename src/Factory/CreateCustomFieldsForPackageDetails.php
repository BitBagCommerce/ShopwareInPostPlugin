<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\CustomField\CustomFieldTypes;

final class CreateCustomFieldsForPackageDetails implements CreateCustomFieldsForPackageDetailsInterface
{
    private EntityRepositoryInterface $customFieldSetRepository;

    public function __construct(EntityRepositoryInterface $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function create(Context $context): void
    {
        $this->customFieldSetRepository->upsert([
            [
                'name' => ShippingMethodPayloadFactoryInterface::SHIPPING_KEY . ' package details',
                'config' => [
                    'label' => [
                        'en-GB' => ShippingMethodPayloadFactoryInterface::SHIPPING_KEY . ' package details',
                    ],
                ],
                'customFields' => [
                    [
                        'name' => 'size',
                        'label' => 'Package size',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Package size',
                            ],
                        ],
                    ],
                    [
                        'name' => 'locker',
                        'label' => 'Package locker',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Package locker',
                            ],
                        ],
                    ],
                ],
                'relations' => [[
                    'entityName' => 'order',
                ]],
            ],
        ], $context);
    }
}
