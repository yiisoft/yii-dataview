<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Widget that renders a text input for setting the page size.
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
            attributes: $attributes,
        )->render();
    }
}
