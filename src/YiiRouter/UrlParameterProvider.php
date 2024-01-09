<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\DataView\UrlParameterPlace;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;

final class UrlParameterProvider implements UrlParameterProviderInterface
{
    public function __construct(private readonly CurrentRoute $currentRoute)
    {
    }

    public function get(string $name, int $place): ?string
    {
        switch ($place) {
            case UrlParameterPlace::PATH:
                return $this->currentRoute->getArgument($name);
            case UrlParameterPlace::QUERY:
                $value = $_GET[$name] ?? null;
                return is_string($value) ? $value : null;
            default:
                return null;
        }
    }
}
