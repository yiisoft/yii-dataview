<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\UrlParameterProviderInterface;
use Yiisoft\Yii\DataView\UrlParameterType;

/**
 * FilterContext provides context information for rendering and handling grid column filters.
 *
 * This immutable class encapsulates all necessary information needed by column renderers
 * to properly render and handle filter inputs in grid columns. It provides access to:
 *
 * Key components:
 * - Form identification for proper form handling
 * - Validation state and error messages
 * - Visual styling configuration for invalid states
 * - URL parameter access for filter persistence
 *
 * Common use cases:
 * - Rendering filter inputs with proper form association
 * - Displaying validation errors
 * - Styling invalid filter cells
 * - Retrieving filter values from URL
 * - Implementing filter persistence
 *
 * Example usage:
 * ```php
 * class CustomFilterRenderer implements FilterableColumnRendererInterface
 * {
 *     public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell
 *     {
 *         // Get current filter value from URL
 *         $value = $context->getQueryValue('filter_' . $column->getName());
 *
 *         // Check for validation errors
 *         $hasError = $context->validationResult->hasErrors();
 *
 *         // Create filter input
 *         $input = Html::textInput()
 *             ->name('filter_' . $column->getName())
 *             ->value($value)
 *             ->form($context->formId);
 *
 *         // Add error styling if needed
 *         if ($hasError && $context->cellInvalidClass !== null) {
 *             $cell = $cell->addClass($context->cellInvalidClass);
 *         }
 *
 *         return $cell->content($input);
 *     }
 * }
 * ```
 */
final class FilterContext
{
    /**
     * Creates a new filter context instance.
     *
     * @param string $formId The unique identifier of the filter form. This ID is used to associate
     *                      filter inputs with their form using the HTML form attribute.
     * @param Result $validationResult The validation result containing any validation errors.
     *                               Use this to check for errors and display appropriate messages.
     * @param string|null $cellInvalidClass CSS class to be applied to cells with invalid filter values.
     *                                    Set to null to disable invalid state styling.
     * @param array $errorsContainerAttributes HTML attributes for the container that displays validation
     *                                       errors. Use these to style and position error messages.
     * @param UrlParameterProviderInterface|null $urlParameterProvider Provider for accessing URL parameters.
     *                                                               Used to retrieve filter values from
     *                                                               the current request.
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
     * This method is typically used to retrieve filter values that were previously
     * set and are present in the current request's URL. It provides a way to
     * maintain filter state across requests.
     *
     * Example:
     * ```php
     * // Get the value of a specific filter
     * $value = $context->getQueryValue('filter_name');
     *
     * // Use the value to set the filter input's default value
     * $input = Html::textInput()
     *     ->name('filter_name')
     *     ->value($value);
     * ```
     *
     * @param string $name The name of the query parameter to retrieve.
     *
     * @return string|null The value of the query parameter or null if not found
     *                    or if no URL parameter provider is configured.
     */
    public function getQueryValue(string $name): ?string
    {
        return $this->urlParameterProvider?->get($name, UrlParameterType::QUERY);
    }
}
