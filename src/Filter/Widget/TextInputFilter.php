<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Html\Html;

final class TextInputFilter extends FilterWidget
{
    public function renderFilter(Context $context): string
    {
        return Html::textInput($context->property, $context->value)
            ->form($context->formId)
            ->render();
    }
}
