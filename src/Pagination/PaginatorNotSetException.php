<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use RuntimeException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

/**
 * Exception thrown when attempting to use a pagination widget without setting a paginator.
 *
 * This exception provides a user-friendly error message and solution through the
 * FriendlyExceptionInterface. It helps developers understand:
 * - What went wrong (missing paginator)
 * - How to fix it (configuration instructions)
 * - Where to find more information (documentation links)
 *
 * Example usage:
 * ```php
 * public function render(): string
 * {
 *     if ($this->paginator === null) {
 *         throw new PaginatorNotSetException();
 *     }
 *     // Render pagination controls
 * }
 * ```
 *
 * @see FriendlyExceptionInterface
 */
final class PaginatorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    /**
     * Creates a new instance with an optional custom message.
     *
     * @param string $message Custom error message. If empty, uses the default message.
     */
    public function __construct(string $message = '')
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message);
    }

    /**
     * Gets the default error message.
     *
     * @return string The error message explaining that the paginator is not set.
     */
    public function getName(): string
    {
        return 'Failed to create widget because "paginator" is not set.';
    }

    /**
     * Gets a user-friendly solution for fixing the error.
     *
     * The solution includes:
     * - Configuration instructions
     * - Available paginator options
     * - Link to documentation
     *
     * @return string|null The solution text in markdown format.
     */
    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `paginator` property in the widget configuration. Use either `OffSetPaginator::class`
            or `KeySetPaginator::class` as paginator.
            For more information [see the documentation](https://github.com/yiisoft/data).
        SOLUTION;
    }
}
