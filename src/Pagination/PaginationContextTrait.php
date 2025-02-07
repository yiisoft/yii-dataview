<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;

/**
 * Trait providing context management functionality for pagination widgets.
 *
 * This trait implements common context handling methods required by all pagination
 * widgets. It provides:
 * - Context storage
 * - Immutable context updates
 * - Context access with validation
 *
 * Example usage:
 * ```php
 * class MyPaginationWidget implements PaginationWidgetInterface
 * {
 *     use PaginationContextTrait;
 *
 *     public function render(): string
 *     {
 *         $context = $this->getContext();
 *         // Use context to generate pagination URLs
 *         return $html;
 *     }
 * }
 * ```
 *
 * @psalm-require-implements PaginationWidgetInterface
 */
trait PaginationContextTrait
{
    private ?PaginationContext $context = null;

    /**
     * Creates a new instance with the specified pagination context.
     *
     * This method follows the immutable pattern, returning a new instance
     * with the updated context rather than modifying the existing instance.
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
     * @throws LogicException if the context has not been set.
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
