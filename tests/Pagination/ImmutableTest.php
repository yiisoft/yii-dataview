<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Yii\DataView\BasePagination;
use Yiisoft\Yii\DataView\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testBasePagination(): void
    {
        $basePagination = new class () extends BasePagination {
            protected function getPaginator(): PaginatorInterface
            {
            }

            protected function getItems(): array
            {
                return [];
            }

            protected function isFirstPage(int|string $page, bool $isPrevious): bool
            {
                return false;
            }
        };
        $this->assertNotSame($basePagination, $basePagination->pageParameterName(''));
        $this->assertNotSame($basePagination, $basePagination->pageSizeParameterName('next'));
        $this->assertNotSame($basePagination, $basePagination->queryParameters([]));
    }

    public function testOffsetPagination(): void
    {
        $offsetPagination = OffsetPagination::widget();
        $this->assertNotSame($offsetPagination, $offsetPagination->maxNavLinkCount(10));
    }
}
