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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="text-danger page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="text-danger page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item disabled"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
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
        <li class="page-item test-disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testFirstPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->firstPageCssClass('test-class')
            ->firstPageLabel('First')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="test-class"><a class="page-link" href data-page="1">First</a></li>
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testCssFrameworkException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid CSS framework. Valid values are: "bootstrap", "bulma".');
        LinkPager::widget()->cssFramework('NoExist');
    }

    public function testHideOnSinglePage(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->hideOnSinglePage()
            ->paginator($this->createOffsetPaginator()->withPageSize(5));
        $this->assertEmpty($linkPager->render());
    }

    public function testLastPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->lastPageCssClass('test-class')
            ->lastPageLabel('Last')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        <li class="test-class"><a class="page-link" href data-page="2">Last</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testLinkAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->linkAttributes(['class' => 'test-class'])
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="test-class" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="test-class" href data-page="1">1</a></li>
        <li class="page-item"><a class="test-class" href data-page="2">2</a></li>
        <li class="page-item"><a class="test-class" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testMaxButtonCount(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->maxButtonCount(1)
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testNextPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->nextPageCssClass('test-class')
            ->nextPageLabel('Next Page')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="test-class"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testNavAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->navAttributes(['class' => 'test-class'])
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav class="test-class">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testPageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->pageCssClass('test-class')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="test-class active"><a class="page-link" href data-page="1">1</a></li>
        <li class="test-class"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testPrevPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->prevPageLabel('Previous')
            ->prevPageCssClass('test-class')
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="test-class disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }

    public function testPaginatorEmpty(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "paginator" property must be set.');
        LinkPager::widget()->render();
    }

    public function testUlAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->ulAttributes(['class' => 'test-class'])
            ->paginator($this->createOffsetPaginator()->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="test-class">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>

        HTML;
        $this->assertEqualsWithoutLE($html, $linkPager->render());
    }
}
