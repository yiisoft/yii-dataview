<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;
use Yiisoft\Data\Paginator\PaginatorInterface;

use function sprintf;

/**
 * Exception thrown when a pagination widget receives an unsupported paginator type.
 *
 * This exception is typically thrown when a widget that expects a specific type
 * of paginator (e.g., KeysetPaginator) receives a different type. It helps
 * developers identify paginator compatibility issues.
 *
 * Example usage:
 * ```php
 * public function withPaginator(PaginatorInterface $paginator): static
 * {
 *     if (!$paginator instanceof KeysetPaginator) {
 *         throw new PaginatorNotSupportedException($paginator);
 *     }
 *     $new = clone $this;
 *     $new->paginator = $paginator;
 *     return $new;
 * }
 * ```
 *
 * The error message will include the class name of the unsupported paginator:
 * "Paginator "SomeOtherPaginator" is not supported."
 */
final class PaginatorNotSupportedException extends LogicException
{
    /**
     * Creates a new instance for the given unsupported paginator.
     *
     * @param PaginatorInterface $paginator The unsupported paginator instance.
     */
    public function __construct(PaginatorInterface $paginator)
    {
        parent::__construct(
            sprintf('Paginator "%s" is not supported.', $paginator::class)
        );
    }
}
