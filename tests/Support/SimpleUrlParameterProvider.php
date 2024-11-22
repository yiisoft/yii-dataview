<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Yii\DataView\UrlParameterProviderInterface;

final class SimpleUrlParameterProvider implements UrlParameterProviderInterface
{
    /**
     * @param string[] $parameters
     */
    public function __construct(
        private readonly array $parameters = [],
    ) {
    }

    public function get(string $name, int $type): ?string
    {
        return $this->parameters[$name] ?? null;
    }
}
