<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

final class UrlQueryReader
{
    public function __construct(
        private ?UrlParameterProviderInterface $urlParameterProvider,
    ) {
    }

    public function get(string $name): ?string
    {
        return $this->urlParameterProvider?->get($name, UrlParameterType::QUERY);
    }
}
