<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\DataView\UrlParameterType;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;

final class UrlParameterProvider implements UrlParameterProviderInterface
{
    public function __construct(private readonly CurrentRoute $currentRoute)
    {
    }

    public function get(string $name, int $type): ?string
    {
        switch ($type) {
            case UrlParameterType::PATH:
                return $this->currentRoute->getArgument($name);
            case UrlParameterType::QUERY:
                $value = $_GET[$name] ?? null;
                return is_string($value) ? $value : null;
            default:
                return null;
        }
    }
}
