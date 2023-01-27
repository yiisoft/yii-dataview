<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\DataView\BasePagination;
use Yiisoft\Yii\DataView\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ImmutableTest extends TestCase
{
    use TestTrait;

    public function testBasePagination(): void
    {
        $basePagination = new class (new CurrentRoute(), Mock::urlGenerator()) extends BasePagination {
            public function render(): string
            {
                return '';
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
        $this->assertNotSame($basePagination, $basePagination->pageConfig([]));
        $this->assertNotSame($basePagination, $basePagination->pageName(''));
        $this->assertNotSame($basePagination, $basePagination->pageSizeName('next'));
        $this->assertNotSame(
            $basePagination,
            $basePagination->paginator($this->createOffsetPaginator([], 10))
        );
        $this->assertNotSame($basePagination, $basePagination->urlArguments([]));
        $this->assertNotSame($basePagination, $basePagination->urlQueryParameters([]));
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testOffsetPagination(): void
    {
        $offsetPagination = OffsetPagination::widget();
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledFirtsPage(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledLastPage(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->disabledPageNavLink(false));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconClassFirtsPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconClassLastPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconFirtsPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->iconLastPage(''));
        $this->assertNotSame($offsetPagination, $offsetPagination->labelFirtsPage());
        $this->assertNotSame($offsetPagination, $offsetPagination->labelLastPage());
        $this->assertNotSame($offsetPagination, $offsetPagination->maxNavLinkCount(10));
    }
}
