<?php

declare(strict_types=1);

namespace BitBag\ShopwareInPostPlugin\Tests\Factory;

use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactory;
use BitBag\ShopwareInPostPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldsForPackageDetailsPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new CustomFieldsForPackageDetailsPayloadFactory();

        self::assertEquals(
            $this->getCustomFieldsData(),
            $factory->create()
        );
    }

    private function getCustomFieldsData(): array
    {
        $customFieldPrefix = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        return [
            'name' => 'Package details',
            'config' => [
                'label' => [
                    'en-GB' => 'Package details',
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
