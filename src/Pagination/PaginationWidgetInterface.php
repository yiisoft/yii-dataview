<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Stringable;
use Yiisoft\Data\Paginator\PaginatorInterface;

interface PaginationWidgetInterface extends Stringable
{
    /**
     * @throws PaginatorNotSupportedException If the paginator is not supported.
     */
    public function withPaginator(PaginatorInterface $paginator): static;

    public function withContext(PaginationContext $context): static;

    public function render(): string;
}
