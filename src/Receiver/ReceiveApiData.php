<?php

declare(strict_types=1);

namespace BitBag\InPost\Receiver;

use Shopware\Core\System\SystemConfig\SystemConfigService;

final class ReceiveApiData implements ReceiveApiDataInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getData(): array
    {
        return [
            'login' => $this->systemConfigService->get('BitBagInPost.config.apiLogin'),
            'password' => $this->systemConfigService->get('BitBagInPost.config.apiPassword'),
        ];
    }
}
