<?php

declare(strict_types=1);

namespace MVenghaus\MagewirePluginWithFileUploads\Helper;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\UrlInterface;

class Security
{
    public function __construct(
        private readonly DeploymentConfig $deployConfig,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    public function generateRouteSignature(string $route, array $params = []): string
    {
        return hash_hmac(
            'sha256',
            $this->urlBuilder->getRouteUrl($route, $params),
            $this->deployConfig->get('crypt/key')
        );
    }

    public function generateRouteSignatureUrl(string $route, array $params = []): string
    {
        return $this->urlBuilder->getRouteUrl(
            $route,
            $params + ['signature' => $this->generateRouteSignature($route, $params)]
        );
    }
}
