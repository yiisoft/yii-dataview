<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Url;

final class NullUrlParameterProvider implements UrlParameterProviderInterface
{
    public function get(string $name, int $type): string|null
    {
        return null;
    }
}
