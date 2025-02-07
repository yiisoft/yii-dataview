<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\UrlParameterType;

/**
 * FilterContext provides context information for rendering and handling grid column filters.
 *
 * This class encapsulates all necessary information for filter rendering and validation:
 *
 * - Form identification
 * - Validation state and results
 * - Visual styling for invalid states
 * - URL parameter handling
 */
final class FilterContext
{
    /**
     * @param string $formId The unique identifier of the filter form.
     * @param Result $validationResult The validation result containing any validation errors.
     * @param string|null $cellInvalidClass CSS class to be applied to cells with invalid filter values.
     * @param array $errorsContainerAttributes HTML attributes for the container that displays validation errors.
     * @param UrlParameterProviderInterface|null $urlParameterProvider Provider for accessing URL parameters.
     */
    public function __construct(
        public readonly string $formId,
        public readonly Result $validationResult,
        public readonly ?string $cellInvalidClass,
        public readonly array $errorsContainerAttributes,
        private readonly ?UrlParameterProviderInterface $urlParameterProvider,
    ) {
    }

    /**
     * Get the value of a query parameter from the URL.
     *
     * @param string $name The name of the query parameter.
     *
     * @return string|null The value of the query parameter or null if not found.
     */
    public function getQueryValue(string $name): ?string
    {
        return $this->urlParameterProvider?->get($name, UrlParameterType::QUERY);
    }
}
