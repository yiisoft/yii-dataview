<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;

/**
 * Trait providing context management functionality for pagination widgets.
 *
 * @psalm-require-implements PaginationWidgetInterface
 */
trait PaginationContextTrait
{
    private ?PaginationContext $context = null;

    /**
     * Creates a new instance with the specified pagination context.
     *
     * @param PaginationContext $context The pagination context to use.
     *
     * @return static New instance with the specified context.
     */
    final public function withContext(PaginationContext $context): static
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    /**
     * Gets the current pagination context.
     *
     * @throws LogicException If the context has not been set.
     *
     * @return PaginationContext The current pagination context.
     */
    final protected function getContext(): PaginationContext
    {
        if ($this->context === null) {
            throw new LogicException('Context is not set.');
        }
        return $this->context;
    }
}
