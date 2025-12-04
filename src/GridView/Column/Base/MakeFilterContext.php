<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\Url\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

/**
 * `MakeFilterContext` provides context for creating and validating grid column filters.
 */
final class MakeFilterContext
{
    /**
     * @param Result $validationResult The validation result containing any validation errors.
     * @param UrlParameterProviderInterface $urlParameterProvider Provider for accessing URL parameters.
     */
    public function __construct(
        public readonly Result $validationResult,
        private readonly UrlParameterProviderInterface $urlParameterProvider,
    ) {}

    /**
     * Get the value of a query parameter from the URL.
     *
     * @param string $name The name of the query parameter.
     *
     * @return string|null The value of the query parameter or null if not found.
     */
    public function getQueryValue(string $name): ?string
    {
        return $this->urlParameterProvider->get($name, UrlParameterType::Query);
    }
}
