<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

interface UrlParameterProviderInterface
{
    /**
     * @psalm-param UrlParameterType::* $type
     */
    public function get(string $name, int $type): ?string;
}
