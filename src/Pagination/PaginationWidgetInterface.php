<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Stringable;
use Yiisoft\Data\Paginator\PaginatorInterface;

/**
 * Interface for widgets that render pagination controls.
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
