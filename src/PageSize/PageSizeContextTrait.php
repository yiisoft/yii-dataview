<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use LogicException;

/**
 * Trait providing context management functionality for page size widgets.
 *
 * @psalm-require-implements PageSizeWidgetInterface
 */
trait PageSizeContextTrait
{
    private ?PageSizeContext $context = null;

    /**
     * Creates a new instance with the specified page size context.
     *
     * @param PageSizeContext $context The page size context to use.
     *
     * @return static New instance with the specified context.
     */
    final public function withContext(PageSizeContext $context): static
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    /**
     * Gets the current page size context.
     *
     * @throws LogicException If the context has not been set.
     *
     * @return PageSizeContext The current page size context.
     */
    final protected function getContext(): PageSizeContext
    {
        if ($this->context === null) {
            throw new LogicException('Context is not set.');
        }
        return $this->context;
    }
}
