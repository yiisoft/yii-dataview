<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Widget\Widget;

abstract class FilterWidget extends Widget
{
    private Context $context;

    final public function withContext(Context $context): self
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    final public function render(): string
    {
        return $this->renderFilter($this->context);
    }

    abstract public function renderFilter(Context $context): string;
}
