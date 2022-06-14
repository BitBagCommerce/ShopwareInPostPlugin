<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $ECSConfig): void {
    $ECSConfig->import('vendor/sylius-labs/coding-standard/ecs.php');

    $parameters = $ECSConfig->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);
};
