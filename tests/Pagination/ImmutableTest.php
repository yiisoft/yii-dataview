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
            public function render(): string
            {
                return '';
            }
            protected function getPaginator(): PaginatorInterface
            {
            }
        };
        $this->assertNotSame($basePagination, $basePagination->attributes([]));
        $this->assertNotSame($basePagination, $basePagination->disabledNextPage(false));
        $this->assertNotSame($basePagination, $basePagination->disabledPreviousPage(false));
        $this->assertNotSame($basePagination, $basePagination->hideOnSinglePage(false));
        $this->assertNotSame($basePagination, $basePagination->iconAttributes([]));
        $this->assertNotSame($basePagination, $basePagination->iconClassNextPage(''));
        $this->assertNotSame($basePagination, $basePagination->iconClassPreviousPage(''));
        $this->assertNotSame($basePagination, $basePagination->iconContainerAttributes([]));
        $this->assertNotSame($basePagination, $basePagination->iconNextPage(''));
        $this->assertNotSame($basePagination, $basePagination->iconPreviousPage(''));
        $this->assertNotSame($basePagination, $basePagination->labelNextPage());
        $this->assertNotSame($basePagination, $basePagination->labelPreviousPage());
        $this->assertNotSame($basePagination, $basePagination->menuClass(''));
        $this->assertNotSame($basePagination, $basePagination->menuItemContainerClass(''));
        $this->assertNotSame($basePagination, $basePagination->pageParameterName(''));
        $this->assertNotSame($basePagination, $basePagination->pageSizeParameterName('next'));
        $this->assertNotSame($basePagination, $basePagination->queryParameters([]));
    }

    public function testOffsetPagination(): void
    {
        $offsetPagination = OffsetPagination::widget();
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledFirstPage(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledLastPage(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledPageNavLink(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconClassFirstPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconClassLastPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconFirstPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconLastPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelFirstPage());
        $this->assertNotSame($offsetPagination, $offsetPagination->labelLastPage());
        $this->assertNotSame($offsetPagination, $offsetPagination->maxNavLinkCount(10));
    }
}
