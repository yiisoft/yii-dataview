<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Nyholm\Psr7\ServerRequest;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\GridView;

final class GridViewTest extends TestCase
{
    public function testAfterItemBeforeItem(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->beforeRow(static fn () => '<div class="testMe">')
            ->afterRow(static fn () => '</div>')
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <div class="testMe">
        <tr></tr>
        </div>
        <div class="testMe">
        <tr></tr>
        </div>
        <div class="testMe">
        <tr></tr>
        </div>
        <div class="testMe">
        <tr></tr>
        </div>
        <div class="testMe">
        <tr></tr>
        </div>
        <div class="testMe">
        <tr></tr>
        </div>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testAutoIdPrefix(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->autoIdPrefix('test')->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="test1-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">No results found.</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testCaption(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->caption('GridView testing.')->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <table class="table table-striped table-bordered"><caption>GridView testing.</caption>

        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">No results found.</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testCaptionOptions(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()
            ->caption('GridView testing.')
            ->captionOptions(['class' => 'text-success'])
            ->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <table class="table table-striped table-bordered"><caption class="text-success">GridView testing.</caption>

        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">No results found.</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testColumnsButtons(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->columns(
                [
                    'id',
                    'username',
                    'total',
                    [
                        '__class' => ActionColumn::class,
                        'header()' => ['Operations'],
                        'buttons' => [
                            'delete' => function ($url) {
                                return Html::a(
                                    Html::tag('span', '&#128465;')->encode(false)->render(),
                                    $url,
                                    [
                                        'class' => 'text-danger',
                                        'data-method' => 'POST',
                                        'data-confirm' => 'Are you sure to delete this user?',
                                        'title' => 'Delete',
                                    ],
                                )->encode(false);
                            },
                        ],
                        'visibleButtons' => [
                            'view' => static fn ($model, $key, $index) => true,
                        ],
                    ],
                ],
            )
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Username</th><th>Total</th><th class="action-column">Operations</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td></td><td>10</td><td>  <a class="text-danger" href="/admin/delete/1" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        <tr><td>2</td><td></td><td>20</td><td>  <a class="text-danger" href="/admin/delete/2" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        <tr><td>3</td><td></td><td>30</td><td>  <a class="text-danger" href="/admin/delete/3" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        <tr><td>4</td><td></td><td>40</td><td>  <a class="text-danger" href="/admin/delete/4" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        <tr><td>5</td><td></td><td>50</td><td>  <a class="text-danger" href="/admin/delete/5" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        <tr><td>6</td><td></td><td>60</td><td>  <a class="text-danger" href="/admin/delete/6" title="Delete" data-method="POST" data-confirm="Are you sure to delete this user?"><span>&#128465;</span></a></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testColumnsException(): void
    {
        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            'The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"'
        );
        $gridView = GridView::widget()->columns([''])->paginator($offsetPaginator)->render();
    }

    public function testDataColumnClass(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()->dataColumnClass(DataColumn::class)->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testPaginatorEmpty(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "paginator" property must be set.');
        GridView::widget()->render();
    }

    public function testEmptyCell(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->columns(['id'])
            ->emptyCell('Empty Cell')
            ->showFooter()
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tfoot>
        <tr><td>Empty Cell</td></tr>
        </tfoot>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        <tr><td>6</td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testEmptyText(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->emptyText('')->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>

        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());

        $gridView = GridView::widget()->emptyText('Not Found')->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">Not Found</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testEmptyTextOptions(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()
            ->emptyText('Not Found')
            ->emptyTextOptions(['class' => 'text-danger'])
            ->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="text-danger">Not Found</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilterOptions(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(
            [
                [
                    'attribute()' => ['id'],
                    'filterAttribute()' => ['id'],
                    'filterValueDefault()' => [0],
                    'filterOptions()' => [['class' => 'text-center']],
                ],
                ['attribute()' => ['name']],
            ]
        );

        $gridView = $gridView
            ->filterModelName('testMe')
            ->filterPosition(GridView::FILTER_POS_HEADER)
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr id="w1-filters" class="filters"><td class="text-center"><input type="text" class="form-control" name="testMe[id]" value="0"></td><td>&nbsp;</td></tr><tr><th>Id</th><th>Name</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td></tr>
        <tr><td>2</td><td>tests 2</td></tr>
        <tr><td>3</td><td>tests 3</td></tr>
        <tr><td>4</td><td>tests 4</td></tr>
        <tr><td>5</td><td>tests 5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());

        $gridView = $gridView
            ->filterModelName('testMe')
            ->filterPosition(GridView::FILTER_POS_FOOTER)
            ->paginator($this->createOffsetPaginator())
            ->showFooter();

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr>
        </thead>
        <tfoot>
        <tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr id="w1-filters" class="filters"><td class="text-center"><input type="text" class="form-control" name="testMe[id]" value="0"></td><td>&nbsp;</td></tr>
        </tfoot>
        <tbody>
        <tr><td>1</td><td>tests 1</td></tr>
        <tr><td>2</td><td>tests 2</td></tr>
        <tr><td>3</td><td>tests 3</td></tr>
        <tr><td>4</td><td>tests 4</td></tr>
        <tr><td>5</td><td>tests 5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilterRowOptions(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(
            [
                [
                    'attribute()' => ['id'],
                    'filterAttribute()' => ['id'],
                    'filterValueDefault()' => [0],
                    'filterOptions()' => [['class' => 'text-center']],
                ],
                ['attribute()' => ['name']],
            ]
        );

        $gridView = $gridView
            ->filterModelName('testMe')
            ->filterRowOptions(['class' => 'text-danger'])
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w1-filters" class="text-danger"><td class="text-center"><input type="text" class="form-control" name="testMe[id]" value="0"></td><td>&nbsp;</td></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td></tr>
        <tr><td>2</td><td>tests 2</td></tr>
        <tr><td>3</td><td>tests 3</td></tr>
        <tr><td>4</td><td>tests 4</td></tr>
        <tr><td>5</td><td>tests 5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFooterRowOptions(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id']);
        $gridView = $gridView
            ->footerRowOptions(['class' => 'text-center'])
            ->paginator($this->createOffsetPaginator())
            ->showFooter();

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tfoot>
        <tr class="text-center"><td>&nbsp;</td></tr>
        </tfoot>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFrameworkCssException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid framework css. Valid values are: "bootstrap", "bulma".');
        $gridView = GridView::widget()->frameworkCss('NoExist');
    }

    public function testId(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->id('testMe')->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="testMe-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">No results found.</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testLayout(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->columns(['id', 'name', 'total'])
            ->layout("{header}\n{summary}\n{items}\n{pager}")
            ->pageSize(5)
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr><td>5</td><td>tests 5</td><td>50</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testShowHeader(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()->showHeader(false)->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <thead>
        </thead>
        HTML;
        $this->assertStringNotContainsString($html, $gridView->render());
    }

    public function testRenderEmpty(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->paginator($offsetPaginator);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view">
        <table class="table table-striped table-bordered">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        <tr><td colspan="0"><div class="empty">No results found.</div></td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testRenderSummary(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->columns(['id', 'name'])
            ->paginator($this->createOffsetPaginator())
            ->Summary('Summary');

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Summary</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td></tr>
        <tr><td>2</td><td>tests 2</td></tr>
        <tr><td>3</td><td>tests 3</td></tr>
        <tr><td>4</td><td>tests 4</td></tr>
        <tr><td>5</td><td>tests 5</td></tr>
        <tr><td>6</td><td>tests 6</td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testRequestAttributes(): void
    {
        GridView::counter(0);

        $request = new ServerRequest('GET', '/admin/index');
        $this->urlMatcher->match($request);

        $gridView = GridView::widget()
            ->columns(['id'])
            ->currentPage(1)
            ->paginator($this->createOffsetPaginator())
            ->pageSize(5)
            ->requestAttributes(['filter' => 1]);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="/admin/index?page=1&amp;filter=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="/admin/index?page=1&amp;filter=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2&amp;filter=1" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2&amp;filter=1" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testRequestQueryParams(): void
    {
        GridView::counter(0);

        $request = new ServerRequest('GET', '/admin/index');
        $this->urlMatcher->match($request);

        $gridView = GridView::widget()
            ->columns(['id'])
            ->currentPage(1)
            ->paginator($this->createOffsetPaginator())
            ->pageSize(5)
            ->requestQueryParams(['filter' => 1]);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="/admin/index?page=1&amp;filter=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="/admin/index?page=1&amp;filter=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2&amp;filter=1" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2&amp;filter=1" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testRowOptions(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id']);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->rowOptions(['class' => 'text-success']);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tbody>
        <tr class="text-success"><td>1</td></tr>
        <tr class="text-success"><td>2</td></tr>
        <tr class="text-success"><td>3</td></tr>
        <tr class="text-success"><td>4</td></tr>
        <tr class="text-success"><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testShowFooter(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id']);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->showFooter();

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tfoot>
        <tr><td>&nbsp;</td></tr>
        </tfoot>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testSummaryOptions(): void
    {
        GridView::counter(0);

        $gridView = GridView::widget()
            ->columns(['id'])
            ->paginator($this->createOffsetPaginator())
            ->Summary('Summary')
            ->SummaryOptions(['class' => 'text-danger']);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div class="text-danger">Summary</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        <tr><td>6</td></tr>
        </tbody>
        </table>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testShowOnEmpty(): void
    {
        GridView::counter(0);

        $dataReader = new IterableDataReader([]);
        $offsetPaginator = new OffsetPaginator($dataReader);

        $gridView = GridView::widget()->paginator($offsetPaginator)->showOnEmpty(false);

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div class="empty">No results found.</div></div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testTableOptions(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id']);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->tableOptions(
            ['class' => 'table text-success']
        );

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table text-success">
        <thead>
        <tr><th>Id</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }
}
