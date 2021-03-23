<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget;

use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\Tests\TestCase;
use Yiisoft\Yii\DataView\Widget\LinkPager;

final class LinkPagerTest extends TestCase
{
    public function testActivePageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->activePageCssClass('test-active')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testButtonsContainerAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->buttonsContainerAttributes(['class' => 'text-danger'])
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="text-danger page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="text-danger page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testDisableCurrentPageButton(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disableCurrentPageButton()
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item disabled"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testDisabledPageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disabledPageCssClass('test-disabled')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));


        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item test-disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testfirstPageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->firstPageCssClass('test-class')
            ->firstPageLabel('First')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="test-class"><a class="page-link" href="" data-page="1">First</a></li>
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testFrameworkCssException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid framework css. Valid values are: "bootstrap", "bulma".');
        LinkPager::widget()->frameworkCss('NoExist');
    }

    public function testHideOnSinglePage(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->hideOnSinglePage()
            ->paginator($this->createOffsetPaginator()->withPageSize(5));
        $this->assertEmpty($linkPager->render());
    }

    public function testPaginatorEmpty(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "paginator" property must be set.');
        LinkPager::widget()->render();
    }
}
