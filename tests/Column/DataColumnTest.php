<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class DataColumnTest extends TestCase
{
    public function testAttribute(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id']],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
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
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testAttributeStringFormat(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id', 'name']);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
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
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testContent(): void
    {
        GridView::counter(0);

        $columns = [
            'id',
            'name',
            [
                'attribute()' => ['total'],
                'content()' => [static fn ($model) => ($model['total'] * 20) / 100],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td><td>2</td></tr>
        <tr><td>2</td><td>tests 2</td><td>4</td></tr>
        <tr><td>3</td><td>tests 3</td><td>6</td></tr>
        <tr><td>4</td><td>tests 4</td><td>8</td></tr>
        <tr><td>5</td><td>tests 5</td><td>10</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testContentIsEmpty(): void
    {
        GridView::counter(0);

        $columns = [
            'id',
            'name',
            [
                'attribute()' => ['total'],
                'content()' => [static fn () => ''],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td><td></td></tr>
        <tr><td>2</td><td>tests 2</td><td></td></tr>
        <tr><td>3</td><td>tests 3</td><td></td></tr>
        <tr><td>4</td><td>tests 4</td><td></td></tr>
        <tr><td>5</td><td>tests 5</td><td></td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testContentOptions(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id']],
            ['attribute()' => ['name'], 'contentOptions()' => [['class' => 'has-text-centered']]],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td class="has-text-centered">tests 1</td></tr>
        <tr><td>2</td><td class="has-text-centered">tests 2</td></tr>
        <tr><td>3</td><td class="has-text-centered">tests 3</td></tr>
        <tr><td>4</td><td class="has-text-centered">tests 4</td></tr>
        <tr><td>5</td><td class="has-text-centered">tests 5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilter(): void
    {
        GridView::counter(0);

        $columns = [
            [
                'attribute()' => ['id'],
                'filter()' => ['<input type="text" class="form-control" name="testMe[id]">'],
                'filterAttribute()' => ['id'],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->filterModelName('testMe');

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w1-filters" class="filters"><td><input type="text" class="form-control" name="testMe[id]"></td><td>&nbsp;</td></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilterInputOptions(): void
    {
        GridView::counter(0);

        $columns = [
            [
                'attribute()' => ['id'],
                'filterAttribute()' => ['id'],
                'filterInputOptions()' => [['class' => 'text-center', 'maxlength' => '5']],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->filterModelName('testMe');

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w1-filters" class="filters"><td><input type="text" class="text-center form-control" name="testMe[id]" maxlength="5"></td><td>&nbsp;</td></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());

        $columns = [
            [
                'attribute()' => ['id'],
                'filterAttribute()' => ['id'],
                'filterInputOptions()' => [['class' => 'text-center', 'maxlength' => '5']],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView
            ->paginator($this->createOffsetPaginator())
            ->filterModelName('testMe')
            ->cssFramework(GridView::BULMA);

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w2-filters" class="filters"><td><input type="text" class="text-center input" name="testMe[id]" maxlength="5"></td><td>&nbsp;</td></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td></tr>
        </tbody>
        </table>
        <nav class="pagination is-centered mt-4" aria-label="Pagination">
        <li class="pagination-previous has-background-link has-text-white" disabled><a href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li><ul class="pagination-list justify-content-center mt-4">
        <li class="pagination-link is-current"><a href data-page="1">1</a></li>
        <li class="pagination-link"><a href data-page="2">2</a></li></ul><li class="pagination-next has-background-link has-text-white"><a href data-page="2">Next Page</a></li>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilterOptions(): void
    {
        GridView::counter(0);

        $columns = [
            [
                'attribute()' => ['id'],
                'filterAttribute()' => ['id'],
                'filterOptions()' => [['class' => 'text-center']],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->filterModelName('testMe');

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w1-filters" class="filters"><td class="text-center"><input type="text" class="form-control" name="testMe[id]"></td><td>&nbsp;</td></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFilterValueDefault(): void
    {
        GridView::counter(0);

        $columns = [
            [
                'attribute()' => ['id'],
                'filterAttribute()' => ['id'],
                'filterValueDefault()' => [0],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->filterModelName('testMe');

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th></tr><tr id="w1-filters" class="filters"><td><input type="text" class="form-control" name="testMe[id]" value="0"></td><td>&nbsp;</td></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFooter(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id']],
            ['attribute()' => ['name']],
            ['attribute()' => ['total'], 'footer()' => ['90']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->showFooter();

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tfoot>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>90</td></tr>
        </tfoot>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testFooterOptions(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id']],
            ['attribute()' => ['name']],
            ['attribute()' => ['total'], 'footer()' => ['90'], 'footerOptions()' => [['class' => 'has-text-link']]],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->showFooter();

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tfoot>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td class="has-text-link">90</td></tr>
        </tfoot>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testHeader(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id']],
            ['attribute()' => ['name'], 'header()' => ['User Name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator())->showFooter();

        $html = <<<'HTML'
        <tr><th>Id</th><th>User Name</th></tr>
        HTML;
        $this->assertStringContainsString($html, $gridView->render());
    }

    public function testHeaderOptions(): void
    {
        GridView::counter(0);

        $columns = [
            ['label()' => ['id'], 'HeaderOptions()' => [['class' => 'has-text-danger']]],
            ['label()' => ['name']],
            ['label()' => ['total']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <tr><th class="has-text-danger">id</th><th>name</th><th>total</th></tr>
        HTML;
        $this->assertStringContainsString($html, $gridView->render());
    }

    public function testLabel(): void
    {
        GridView::counter(0);

        $columns = [
            ['label()' => ['id']],
            ['label()' => ['name']],
            ['label()' => ['total']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <tr><th>id</th><th>name</th><th>total</th></tr>
        HTML;
        $this->assertStringContainsString($html, $gridView->render());
    }

    public function testLabelEmpty(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView(['id', 'name', 'total']);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        HTML;
        $this->assertStringContainsString($html, $gridView->render());
    }

    public function testNotEncodeLabel(): void
    {
        GridView::counter(0);

        $columns = [
            ['attribute()' => ['id'], 'label()' => ['<id>']],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>&lt;id&gt;</th><th>Name</th></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());

        $columns = [
            [
                'attribute()' => ['id'],
                'label()' => ['<i class="fas fa-home">id</i>'],
                'notEncodeLabel()' => [],
            ],
            ['attribute()' => ['name']],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th><i class="fas fa-home">id</i></th><th>Name</th></tr>
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
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testSorting(): void
    {
        GridView::counter(0);

        $this->currentRoute->setRouteWithArguments(
            Route::methods(['GET', 'POST'], '/admin/index')->action([TestDelete::class, 'run'])->name('admin'),
            []
        );

        $gridView = $this->createGridView(['id', 'name']);
        $gridView = $gridView->paginator($this->createOffsetPaginator(['id', 'name']));

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th><a class="asc" href="/admin/index?page=1&amp;sort=-id" data-sort="-id">Id</a></th><th><a class="asc" href="/admin/index?page=1&amp;sort=-name" data-sort="-name">Name</a></th></tr>
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
        <li class="page-item disabled"><a class="page-link" href="/admin/index?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="/admin/index?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());


        $gridView = $this->createGridView(['id', 'name']);
        $gridView = $gridView
            ->cssFramework(GridView::BULMA)
            ->paginator($this->createOffsetPaginator(['id', 'name']));

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th><a class="asc has-text-link" href="/admin/index?page=1&amp;sort=-id" data-sort="-id">Id</a></th><th><a class="asc has-text-link" href="/admin/index?page=1&amp;sort=-name" data-sort="-name">Name</a></th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td></tr>
        </tbody>
        </table>
        <nav class="pagination is-centered mt-4" aria-label="Pagination">
        <li class="pagination-previous has-background-link has-text-white" disabled><a href="/admin/index?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li><ul class="pagination-list justify-content-center mt-4">
        <li class="pagination-link is-current"><a href="/admin/index?page=1" data-page="1">1</a></li>
        <li class="pagination-link"><a href="/admin/index?page=2" data-page="2">2</a></li></ul><li class="pagination-next has-background-link has-text-white"><a href="/admin/index?page=2" data-page="2">Next Page</a></li>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testSortingDisable(): void
    {
        GridView::counter(0);

        $this->currentRoute->setRouteWithArguments(
            Route::methods(['GET', 'POST'], '/admin/index')->action([TestDelete::class, 'run'])->name('admin'),
            []
        );

        $gridView = $this->createGridView(
            [
                'id',
                ['attribute()' => ['name'], 'disableSorting()' => []],
                'total',
            ]
        );
        $gridView = $gridView->paginator($this->createOffsetPaginator(['id', 'name']));

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th><a class="asc" href="/admin/index?page=1&amp;sort=-id" data-sort="-id">Id</a></th><th>Name</th><th>Total</th></tr>
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
        <li class="page-item disabled"><a class="page-link" href="/admin/index?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="/admin/index?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testSortLinkOption(): void
    {
        GridView::counter(0);

        $this->currentRoute->setRouteWithArguments(
            Route::methods(['GET', 'POST'], '/admin/index')->action([TestDelete::class, 'run'])->name('admin'),
            []
        );

        $gridView = $this->createGridView(
            [
                ['attribute()' => ['id'], 'sortLinkOptions()' => [['class' => 'testMe']]],
                'name',
                'total',
            ]
        );
        $gridView = $gridView->paginator($this->createOffsetPaginator(['id', 'name']));

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th><a class="testMe asc" href="/admin/index?page=1&amp;sort=-id" data-sort="-id">Id</a></th><th><a class="asc" href="/admin/index?page=1&amp;sort=-name" data-sort="-name">Name</a></th><th>Total</th></tr>
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
        <li class="page-item disabled"><a class="page-link" href="/admin/index?page=1" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="/admin/index?page=1" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="/admin/index?page=2" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }

    public function testInvisible(): void
    {
        GridView::counter(0);

        $columns = [
            ['label()' => ['id']],
            ['label()' => ['name']],
            ['label()' => ['total'], 'invisible()' => []],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());
        $this->assertStringNotContainsString('total', $gridView->render());
    }

    public function testOptions(): void
    {
        GridView::counter(0);

        $columns = [
            ['label()' => ['id']],
            ['label()' => ['name']],
            ['label()' => ['total'], 'options()' => [['class' => 'testMe']]],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());
        $this->assertStringContainsString('class="testMe"', $gridView->render());
    }

    public function testValue(): void
    {
        GridView::counter(0);

        $columns = [
            'id',
            'name',
            [
                'attribute()' => ['total'],
                'value()' => [
                    static fn ($model): string => $model['total'] === '50'
                        ? '*' . $model['total'] . '*' : $model['total'],
                ],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr><td>5</td><td>tests 5</td><td>*50*</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());

        $columns = [
            'id',
            'username',
            [
                'attribute()' => ['total'],
                'value()' => ['id'],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Username</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td></td><td>1</td></tr>
        <tr><td>2</td><td></td><td>2</td></tr>
        <tr><td>3</td><td></td><td>3</td></tr>
        <tr><td>4</td><td></td><td>4</td></tr>
        <tr><td>5</td><td></td><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }
}
