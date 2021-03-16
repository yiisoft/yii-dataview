<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class DataColumnTest extends TestCase
{
    public function testRender(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumns());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
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

        $this->assertEqualsWithoutLE($expected, $html);
    }

    public function testRenderColumString(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumnsString());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
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

        $this->assertEqualsWithoutLE($expected, $html);
    }

    public function testRenderColumMissingLabel(): void
    {
        GridView::counter(0);

        $gridView = $this->createGridView($this->createColumnsMissingLabel());
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = $gridView->render();

        $expected = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th></tr>
        </thead>
        <tbody>
        <tr data-key="0"><td>1</td><td>tests 1</td><td>10</td></tr>
        <tr data-key="1"><td>2</td><td>tests 2</td><td>20</td></tr>
        <tr data-key="2"><td>3</td><td>tests 3</td><td>30</td></tr>
        <tr data-key="3"><td>4</td><td>tests 4</td><td>40</td></tr>
        <tr data-key="4"><td>5</td><td>tests 5</td><td>50</td></tr>
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

        $this->assertEqualsWithoutLE($expected, $html);
    }

    private function createColumns(): array
    {
        return [
            [
                'attribute()' => ['id'],
                'label()' => ['Id'],
            ],
            [
                'attribute()' => ['name'],
                'label()' => ['Name'],
            ],
            [
                'attribute()' => ['total'],
                'label()' => ['Total'],
            ],
        ];
    }

    private function createColumnsString(): array
    {
        return [
            'id:text:Id',
            'name:text:Name',
            'total:text:Total',
        ];
    }

    private function createColumnsMissingLabel(): array
    {
        return [
            [
                'attribute()' => ['id'],
            ],
            [
                'attribute()' => ['name'],
            ],
            [
                'attribute()' => ['total'],
            ],
        ];
    }
}
