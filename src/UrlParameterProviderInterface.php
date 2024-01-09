<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

interface UrlParameterProviderInterface
{
    /**
     * @psalm-param UrlParameterPlace::* $place
     */
    public function get(string $name, int $place): ?string;
}
