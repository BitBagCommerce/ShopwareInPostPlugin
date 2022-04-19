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
            $this->customFieldsData(),
            $factory->create()
        );
    }

    private function customFieldsData(): array
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
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Insurance value (can be left empty)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_height',
                    'label' => 'Height',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Height',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_width',
                    'label' => 'Width',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Width',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_depth',
                    'label' => 'Depth',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Depth',
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
