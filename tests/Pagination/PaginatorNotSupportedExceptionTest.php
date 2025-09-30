<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;

final class PaginatorNotSupportedExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([]));
        $exception = new PaginatorNotSupportedException($paginator);

        $this->assertSame(
            'Paginator "Yiisoft\Data\Paginator\OffsetPaginator" is not supported.',
            $exception->getMessage(),
        );
    }
}
