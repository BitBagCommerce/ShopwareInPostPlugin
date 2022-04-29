<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Factory;

use Shopware\Core\System\CustomField\CustomFieldTypes;

final class CustomFieldsForPackageDetailsPayloadFactory implements CustomFieldsForPackageDetailsPayloadFactoryInterface
{
    public function create(): array
    {
        $customFieldPrefix = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        return [
            'name' => 'Package details',
            'config' => [
                'label' => [
                    'en-GB' => 'Package details',
                    'pl-PL' => 'Szczegóły paczki',
                ],
                'technical_name' => $customFieldPrefix,
            ],
            'customFields' => [
                [
                    'name' => $customFieldPrefix . '_insurance',
                    'label' => 'Insurance value (can be left empty)',
                    'type' => CustomFieldTypes::FLOAT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Insurance value (can be left empty)',
                            'pl-PL' => 'Wartość ubezpieczenia (może zostać puste)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_height',
                    'label' => 'Height (cm)',
                    'type' => CustomFieldTypes::FLOAT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Height (cm)',
                            'pl-PL' => 'Wysokość (cm)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_width',
                    'label' => 'Width (cm)',
                    'type' => CustomFieldTypes::FLOAT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Width (cm)',
                            'pl-PL' => 'Szerokość (cm)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_depth',
                    'label' => 'Depth (cm)',
                    'type' => CustomFieldTypes::FLOAT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Depth (cm)',
                            'pl-PL' => 'Głębokość (cm)',
                        ],
                    ],
                ],
            ],
            'relations' => [[
                'entityName' => 'order',
            ]],
        ];
    }
}
