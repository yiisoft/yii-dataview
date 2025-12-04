<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\Url\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

/**
 * `FilterContext` provides context information for rendering and handling grid column filters.
 */
final class FilterContext
{
    /**
     * Creates a new filter context instance.
     *
     * @param string $formId The unique identifier of the filter form. This ID is used to associate
     * filter inputs with their form using the HTML form attribute.
     * @param Result $validationResult The validation result containing any validation errors.
     * Use this to check for errors and display appropriate messages.
     * @param string|null $cellInvalidClass CSS class to be applied to cells with invalid filter values.
     * Set to null to disable invalid state styling.
     * @param array $errorsContainerAttributes HTML attributes for the container that displays validation
     * errors. Use these to style and position error messages.
     * @param UrlParameterProviderInterface $urlParameterProvider Provider for accessing URL parameters.
     * Used to retrieve filter values from the current request.
     */
    public function __construct(
        public readonly string $formId,
        public readonly Result $validationResult,
        public readonly ?string $cellInvalidClass,
        public readonly array $errorsContainerAttributes,
        private readonly UrlParameterProviderInterface $urlParameterProvider,
    ) {}

    /**
     * Get the value of a query parameter from the URL.
     *
     * @param string $name The name of the query parameter to retrieve.
     *
     * @return string|null The value of the query parameter or `null` if not found
     * or if no URL parameter provider is configured.
     */
    public function getQueryValue(string $name): ?string
    {
        return $this->urlParameterProvider->get($name, UrlParameterType::Query);
    }
}
