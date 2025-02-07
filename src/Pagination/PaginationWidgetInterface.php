<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Stringable;
use Yiisoft\Data\Paginator\PaginatorInterface;

/**
 * Interface for widgets that render pagination controls.
 *
 * This interface defines the contract for pagination widgets, which are responsible for:
 * - Rendering pagination controls (links, buttons, etc.)
 * - Managing pagination context and state
 * - Supporting different paginator implementations
 *
 * Implementations should:
 * - Use immutable state management
 * - Support context-based configuration
 * - Handle paginator-specific requirements
 * - Render valid HTML controls
 *
 * Example usage:
 * ```php
 * class MyPaginationWidget implements PaginationWidgetInterface
 * {
 *     private ?PaginatorInterface $paginator = null;
 *     private ?PaginationContext $context = null;
 *
 *     public function withPaginator(PaginatorInterface $paginator): static
 *     {
 *         $new = clone $this;
 *         $new->paginator = $paginator;
 *         return $new;
 *     }
 *
 *     public function withContext(PaginationContext $context): static
 *     {
 *         $new = clone $this;
 *         $new->context = $context;
 *         return $new;
 *     }
 *
 *     public function render(): string
 *     {
 *         // Render pagination controls
 *         return $html;
 *     }
 * }
 * ```
 *
 * @see KeysetPagination A keyset-based pagination implementation
 * @see OffsetPagination An offset-based pagination implementation
 */
interface PaginationWidgetInterface extends Stringable
{
    /**
     * Creates a new instance with the specified paginator.
     *
     * @param PaginatorInterface $paginator The paginator to use.
     *
     * @throws PaginatorNotSupportedException If the paginator type is not supported
     * by the implementation.
     *
     * @return static New instance with the specified paginator.
     */
    public function withPaginator(PaginatorInterface $paginator): static;

    /**
     * Creates a new instance with the specified pagination context.
     *
     * @param PaginationContext $context The pagination context to use.
     *
     * @return static New instance with the specified context.
     */
    public function withContext(PaginationContext $context): static;

    /**
     * Renders the pagination controls.
     *
     * @return string The rendered HTML pagination controls.
     */
    public function render(): string;
}
