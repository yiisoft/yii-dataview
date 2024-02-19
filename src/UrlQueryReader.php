<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class UrlQueryReader
{
    public function get(string $name): ?string
    {
        $value = $_GET[$name] ?? null;
        return is_string($value) ? $value : null;
    }
}
