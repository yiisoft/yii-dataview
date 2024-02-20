<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\UrlQueryReader;

final class FilterContext
{
    private readonly UrlQueryReader $urlQueryReader;

    public function __construct(
        public readonly string $formId,
        public readonly Result $validationResult,
        public readonly ?string $cellInvalidClass,
        public readonly array $errorsContainerAttributes,
    ) {
        $this->urlQueryReader = new UrlQueryReader();
    }

    public function getQueryValue(string $name): ?string
    {
        return $this->urlQueryReader->get($name);
    }
}
