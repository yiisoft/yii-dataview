<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

use function count;
use function is_array;

/**
 * Widget that renders a dropdown (select) input for choosing the page size.
 *
 * This widget creates an HTML select element that allows users to choose from
 * predefined page size options. It includes:
 * - Automatic option generation from constraints
 * - Client-side URL updates
 * - Default value handling
 *
 * Example usage:
 * ```php
 * echo SelectPageSize::widget()
 *     ->addAttributes(['class' => 'form-select'])
 *     ->withContext(new PageSizeContext(
 *         currentValue: 20,
 *         defaultValue: 10,
 *         constraint: [10, 20, 50, 100],
 *         urlPattern: '/users?pageSize=' . PageSizeContext::URL_PLACEHOLDER,
 *         defaultUrl: '/users'
 *     ));
 * ```
 *
 * The above example will render:
 * ```html
 * <select class="form-select"
 *     data-default-page-size="10"
 *     data-url-pattern="/users?pageSize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER"
 *     data-default-url="/users"
 *     onchange="window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace('YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER', this.value)">
 *     <option value="10">10</option>
 *     <option value="20" selected>20</option>
 *     <option value="50">50</option>
 *     <option value="100">100</option>
 * </select>
 * ```
 */
final class SelectPageSize extends Widget implements PageSizeWidgetInterface
{
    use PageSizeContextTrait;

    private array $attributes = [];

    /**
     * Add a set of attributes to existing select tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'form-select', 'aria-label' => 'Items per page']`
     *
     * @return self New instance with added attributes.
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = array_merge($new->attributes, $attributes);
        return $new;
    }

    /**
     * Replace select tag attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'page-size-select', 'required' => true]`
     *
     * @return self New instance with replaced attributes.
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;
        return $new;
    }

    /**
     * Renders the page size select with the current context.
     *
     * The rendered select includes:
     * - Options generated from the constraint array
     * - Current page size as the selected value
     * - Data attributes for default page size and URLs
     * - JavaScript for handling page size changes
     *
     * Note: If the constraint is not an array or has less than 2 options,
     * an empty string is returned as a select with only one option is not useful.
     *
     * @return string The rendered HTML select element, or empty string if
     * there are insufficient options.
     */
    public function render(): string
    {
        $context = $this->getContext();
        if (!is_array($context->constraint) || count($context->constraint) < 2) {
            return '';
        }

        $options = [];
        foreach ($context->constraint as $value) {
            $options[$value] = (string) $value;
        }

        $attributes = array_merge($this->attributes, [
            'data-default-page-size' => $context->defaultValue,
            'data-url-pattern' => $context->urlPattern,
            'data-default-url' => $context->defaultUrl,
            'onchange' => 'window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace("' . PageSizeContext::URL_PLACEHOLDER . '", this.value)',
        ]);

        return Html::select()
            ->optionsData($options, encode: false)
            ->value($context->currentValue)
            ->attributes($attributes)
            ->render();
    }
}
