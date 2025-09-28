<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Yii\DataView\Url\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

final class SimpleUrlParameterProvider implements UrlParameterProviderInterface
{
    private array $parameters;

    /**
     * @param string[] $query
     * @param string[] $path
     */
    public function __construct(
        array $query = [],
        array $path = [],
    ) {
        $this->parameters = [
            UrlParameterType::QUERY => $query,
            UrlParameterType::PATH => $path,
        ];
    }

    public function get(string $name, int $type): ?string
    {
        return $this->parameters[$type][$name] ?? null;
    }
}
