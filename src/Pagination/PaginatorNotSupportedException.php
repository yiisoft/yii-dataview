<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;
use Yiisoft\Data\Paginator\PaginatorInterface;

use function sprintf;

final class PaginatorNotSupportedException extends LogicException
{
    public function __construct(PaginatorInterface $paginator)
    {
        parent::__construct(
            sprintf('Paginator "%s" is not supported.', $paginator::class)
        );
    }
}
