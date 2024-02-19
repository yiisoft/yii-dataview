<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Yii\DataView\UrlQueryReader;

final class FilterContext
{
    private readonly UrlQueryReader $urlQueryReader;

    public function __construct(
        public readonly string $formId,
    ) {
        $this->urlQueryReader = new UrlQueryReader();
    }

    public function getQueryValue(string $name): ?string
    {
        return $this->urlQueryReader->get($name);
    }
}
