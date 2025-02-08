<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;
use Yiisoft\Data\Paginator\PaginatorInterface;

use function sprintf;

/**
 * Exception thrown when a pagination widget receives an unsupported paginator type.
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
