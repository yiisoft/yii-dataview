<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\UrlParameterType;

final class FilterContext
{
    public function __construct(
        public readonly string $formId,
        public readonly Result $validationResult,
        public readonly ?string $cellInvalidClass,
        public readonly array $errorsContainerAttributes,
        private readonly ?UrlParameterProviderInterface $urlParameterProvider,
    ) {
    }

    public function getQueryValue(string $name): ?string
    {
        return $this->urlParameterProvider?->get($name, UrlParameterType::QUERY);
    }
}
