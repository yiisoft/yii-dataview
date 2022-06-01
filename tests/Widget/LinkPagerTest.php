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
            ->activeButtonAttributes(['class' => 'test-active'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());

        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->activeButtonAttributes(['data-active' => true])
            ->activePageCssClass('test-active')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active" data-active><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testButtonsContainerAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->buttonsContainerAttributes(['class' => 'text-danger page-item'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="text-danger page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="text-danger page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testDisableCurrentPageButton(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disableCurrentPageButton()
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active disabled"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testDisabledPageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disabledButtonAttributes(['class' => 'test-disabled'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));


        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item test-disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());

        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disabledButtonAttributes(['data-disabled' => true])
            ->disabledPageCssClass('test-disabled')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item test-disabled" data-disabled><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;

        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testFirstPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->firstPageAttributes([
                'class' => 'test-class',
            ])
            ->firstPageLabel('First')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="test-class disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">First</a></li>
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
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
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));
        $this->assertEmpty($linkPager->render());
    }

    public function testLastPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->lastPageAttributes([
                'class' => 'test-class',
            ])
            ->lastPageLabel('Last')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        <li class="test-class"><a class="page-link" href="?page=2" data-page="2">Last</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testLinkAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->linkAttributes(['class' => 'test-class'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="test-class" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="test-class" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="test-class" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="test-class" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testMaxButtonCount(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->maxButtonCount(1)
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testNextPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->nextPageAttributes(['class' => 'test-class'])
            ->nextPageLabel('Next Page')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="test-class"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());

        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->nextPageAttributes(['data-next' => true])
            ->nextPageCssClass('next')
            ->nextPageLabel('Next Page')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="next" data-next><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testNavAttributes(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->navAttributes(['class' => 'test-class'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav class="test-class">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testPageCssClass(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->buttonsContainerAttributes(['class' => 'test-class'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="test-class active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="test-class"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());

        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->buttonsContainerAttributes(['data-test' => true])
            ->pageCssClass('test-class')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="test-class active" data-test><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="test-class" data-test><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testPrevPageLabel(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->prevPageLabel('Previous')
            ->prevPageAttributes(['class' => 'test-class'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="test-class disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());

        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->prevPageLabel('Previous')
            ->prevPageAttributes(['data-prev' => true])
            ->prevPageCssClass('prev')
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="prev disabled" data-prev><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
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
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="test-class">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testEncode(): void
    {
        $linkPager = LinkPager::widget()
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5))
            ->prevPageLabel('<span aria-hidden="true">&laquo;</span>')
            ->nextPageLabel('<span aria-hidden="true">&raquo;</span>')
            ->prevPageAttributes([
                'class' => 'page-item',
                'encode' => false,
            ])
            ->nextPageAttributes([
                'class' => 'page-item',
                'encode' => false,
            ]);

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1"><span aria-hidden="true">&laquo;</span></a></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2"><span aria-hidden="true">&raquo;</span></a></li>
        </ul>
        </nav>
        HTML;

        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testActiveAndDisabledTag(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->disabledLinkAttributes([
                'tag' => 'span',
            ])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><span class="page-link" data-page="1" aria-disabled="true" tabindex="-1">Previous</span></li>
        <li class="page-item active"><a class="page-link" href="?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());


        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->activeLinkAttributes([
                'tag' => 'span',
            ])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><span class="page-link" data-page="1">1</span></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testQueryParams(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5))
            ->requestQueryParams([
                'param1' => 'foo',
                'param2' => 'bar',
                'paramArray' => [
                    'foo',
                    'bar',
                ],
            ]);

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?param1=foo&amp;param2=bar&amp;paramArray%5B0%5D=foo&amp;paramArray%5B1%5D=bar&amp;page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="?param1=foo&amp;param2=bar&amp;paramArray%5B0%5D=foo&amp;paramArray%5B1%5D=bar&amp;page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?param1=foo&amp;param2=bar&amp;paramArray%5B0%5D=foo&amp;paramArray%5B1%5D=bar&amp;page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?param1=foo&amp;param2=bar&amp;paramArray%5B0%5D=foo&amp;paramArray%5B1%5D=bar&amp;page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;

        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testCustomMarkup(): void
    {
        LinkPager::counter(0);

        $avito = LinkPager::widget()
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5))
            ->navAttributes([
                'class' => 'b-shop-pagination',
            ])
            ->ulAttributes([
                'tag' => 'div',
                'class' => 'pagination js-pages',
            ])
            ->buttonsContainerAttributes([
                'tag' => null,
            ])
            ->nextPageAttributes([
                'tag' => 'div',
                'class' => 'pagination-nav clearfix',
            ])
            ->linkAttributes([
                'class' => 'pagination-page',
            ])
            ->lastPageAttributes([
                'tag' => null,
            ])
            ->listTemplate(
                LinkPager::NEXT_PAGE_BUTTON .
                '<div class="pagination-pages clearfix">' .
                LinkPager::PAGES .
                LinkPager::LAST_PAGE_BUTTON .
                '</div>'
            )
            ->lastPageLabel('Last Page');

        $avito2 = $avito
            ->ulAttributes([
                'tag' => 'div',
                'class' => 'pagination-pages clearfix',
            ])
            ->template(
                '<div class="pagination js-pages">' .
                LinkPager::NEXT_PAGE_BUTTON .
                LinkPager::PAGE_LIST .
                '</div>'
            )
            ->listTemplate(
                LinkPager::PAGES .
                LinkPager::LAST_PAGE_BUTTON
            );

        $html = <<<'HTML'
        <nav class="b-shop-pagination">
        <div class="pagination js-pages">
        <div class="pagination-nav clearfix"><a class="pagination-page" href="?page=2" data-page="2">Next Page</a></div>
        <div class="pagination-pages clearfix">
        <a class="pagination-page" href="?page=1" data-page="1">1</a>
        <a class="pagination-page" href="?page=2" data-page="2">2</a>
        <a class="pagination-page" href="?page=2" data-page="2">Last Page</a>
        </div>
        </div>
        </nav>
        HTML;

        $this->assertEqualsHTML($html, $avito->render());
        $this->assertEqualsHTML($html, $avito2->render());
    }

    public function testCustomPageParam(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->pageParam('my-custom-page-param')
            ->activeButtonAttributes(['class' => 'test-active'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="?my-custom-page-param=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active"><a class="page-link" href="?my-custom-page-param=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?my-custom-page-param=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?my-custom-page-param=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testPageArgument(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->pageArgument(true)
            ->activeButtonAttributes(['class' => 'test-active'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

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
        $this->assertEqualsHTML($html, $linkPager->render());
    }

    public function testHideFirstPageParam(): void
    {
        LinkPager::counter(0);

        $linkPager = LinkPager::widget()
            ->hideFirstPageParameter()
            ->activeButtonAttributes(['class' => 'test-active'])
            ->paginator($this
                ->createOffsetPaginator()
                ->withPageSize(5));

        $html = <<<'HTML'
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item test-active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        HTML;
        $this->assertEqualsHTML($html, $linkPager->render());
    }
}
