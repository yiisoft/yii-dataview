<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input;

/**
 * Filter widget that renders a text input for filtering data.
 *
 * This widget creates a standard HTML text input that can be used for free-form
 * text filtering in data views. It supports customization through HTML attributes
 * and follows immutable object pattern.
 *
 * Example usage:
 * ```php
 * echo TextInputFilter::widget()
 *     ->addAttributes(['class' => 'form-control'])
 *     ->withContext(new Context('username', 'john', 'filter-form'));
 * ```
 *
 * The above example will render:
 * ```html
 * <input type="text" class="form-control" name="username" value="john" form="filter-form">
 * ```
 */
final class TextInputFilter extends FilterWidget
{
    private Input $input;

    /**
     * Add a set of attributes to existing tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'form-control', 'placeholder' => 'Search...']`
     *
     * @return self New instance with added attributes.
     *
     * @see Input::addAttributes()
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->input = $this->getInput()->addAttributes($attributes);
        return $new;
    }

    /**
     * Replace attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'search-input', 'data-role' => 'filter']`
     *
     * @return self New instance with replaced attributes.
     *
     * @see Input::attributes()
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->input = $this->getInput()->attributes($attributes);
        return $new;
    }

    /**
     * Renders the text input filter with the given context.
     *
     * Uses the context to set:
     * - name attribute from context property
     * - value attribute from context value
     * - form attribute from context formId
     *
     * @param Context $context The filter context.
     *
     * @return string The rendered HTML input element.
     */
    public function renderFilter(Context $context): string
    {
        return $this->getInput()
            ->name($context->property)
            ->value($context->value)
            ->form($context->formId)
            ->render();
    }

    /**
     * Gets the input instance, creating a new one if not set.
     *
     * @return Input The HTML input instance.
     */
    private function getInput(): Input
    {
        return $this->input ?? Html::textInput();
    }
}
