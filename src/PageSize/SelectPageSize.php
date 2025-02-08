<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

use function count;
use function is_array;

/**
 * Widget that renders a dropdown (select) input for choosing the page size.
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
