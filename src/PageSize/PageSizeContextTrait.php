<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use LogicException;

/**
 * @psalm-require-implements PageSizeWidgetInterface
 */
trait PageSizeContextTrait
{
    private ?PageSizeContext $context = null;

    final public function withContext(PageSizeContext $context): static
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    final protected function getContext(): PageSizeContext
    {
        if ($this->context === null) {
            throw new LogicException('Context is not set.');
        }
        return $this->context;
    }
}
