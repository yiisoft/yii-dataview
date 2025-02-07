<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Widget that renders a text input for setting the page size.
 *
 * This widget creates an HTML text input that allows users to manually enter
 * their desired page size. It includes:
 * - Client-side validation
 * - Automatic URL updates
 * - Default value handling
 *
 * Example usage:
 * ```php
 * echo InputPageSize::widget()
 *     ->addAttributes(['class' => 'form-control'])
 *     ->withContext(new PageSizeContext(
 *         currentValue: 20,
 *         defaultValue: 10,
 *         constraint: PageSizeContext::ANY_VALUE,
 *         urlPattern: '/users?pageSize=' . PageSizeContext::URL_PLACEHOLDER,
 *         defaultUrl: '/users'
 *     ));
 * ```
 *
 * The above example will render:
 * ```html
 * <input type="text" class="form-control" value="20"
 *     data-default-page-size="10"
 *     data-url-pattern="/users?pageSize=YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER"
 *     data-default-url="/users"
 *     onchange="window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace('YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER', this.value)">
 * ```
 */
final class InputPageSize extends Widget implements PageSizeWidgetInterface
{
    use PageSizeContextTrait;

    private array $attributes = [];

    /**
     * Add a set of attributes to existing input tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'form-control', 'placeholder' => 'Items per page']`
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
     * Replace input tag attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'page-size-input', 'required' => true]`
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
     * Renders the page size input with the current context.
     *
     * The rendered input includes:
     * - Current page size as the value
     * - Data attributes for default page size and URLs
     * - JavaScript for handling page size changes
     *
     * @return string The rendered HTML input element.
     */
    public function render(): string
    {
        $context = $this->getContext();

        $attributes = array_merge($this->attributes, [
            'data-default-page-size' => $context->defaultValue,
            'data-url-pattern' => $context->urlPattern,
            'data-default-url' => $context->defaultUrl,
            'onchange' => 'window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace("' . PageSizeContext::URL_PLACEHOLDER . '", this.value)',
        ]);

        return Html::textInput(
            value: $context->currentValue,
            attributes: $attributes
        )->render();
    }
}
