<?php

namespace Yiisoft\Yii\DataView\Tests\Stubs;

use Yiisoft\Router\RouteNotFoundException;
use Yiisoft\Router\UrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    private string $urlPrefix = '';

    public function generate(string $name, array $parameters = []): string
    {
        return $name;
    }

    public function getUriPrefix(): string
    {
        return $this->urlPrefix;
    }

    public function setUriPrefix(string $name): void
    {
        $this->urlPrefix = $name;
    }

    public function generateAbsolute(
        string $name,
        array $parameters = [],
        string $scheme = null,
        string $host = null
    ): string {
        return $name;
    }
}
