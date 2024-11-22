<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

use function count;
use function is_array;

final class SelectPageSize extends Widget implements PageSizeWidgetInterface
{
    use PageSizeContextTrait;

    private array $attributes = [];

    /**
     * Add a set of attributes to existing SELECT tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = array_merge($new->attributes, $attributes);
        return $new;
    }

    /**
     * Replace SELECT tag attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->attributes = $attributes;
        return $new;
    }

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
            'onchange' => 'window.location.href = this.value == this.dataset.defaultPageSize ? this.dataset.defaultUrl : this.dataset.urlPattern.replace("' . PageSizeContext::URL_PLACEHOLDER . '", this.value)'
        ]);

        return Html::select()
            ->optionsData($options, encode: false)
            ->value($context->currentValue)
            ->attributes($attributes)
            ->render();
    }
}
