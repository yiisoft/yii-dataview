<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input;

final class TextInputFilter extends FilterWidget
{
    private Input $input;

    /**
     * Add a set of attributes to existing tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
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
     *
     * @see Input::attributes()
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->input = $this->getInput()->attributes($attributes);
        return $new;
    }

    public function renderFilter(Context $context): string
    {
        return $this->getInput()
            ->name($context->property)
            ->value($context->value)
            ->form($context->formId)
            ->render();
    }

    private function getInput(): Input
    {
        return $this->input ?? Html::textInput();
    }
}
