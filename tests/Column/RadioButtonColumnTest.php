<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class RadioButtonColumnTest extends TestCase
{
    public function testContentIsEmpty(): void
    {
        GridView::counter(0);

        $columns = [
            [
                '__class' => RadioButtonColumn::class,
                'content' => static fn () => '',
                'header()' => ['x'],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>x</th></tr>
        </thead>
        <tbody>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
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

    public function testName(): void
    {
        GridView::counter(0);

        $columns = [
            [
                '__class' => RadioButtonColumn::class,
                'name()' => ['testMe'],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>&nbsp;</th></tr>
        </thead>
        <tbody>
        <tr><td><input type="radio" name="testMe" value="0"></td></tr>
        <tr><td><input type="radio" name="testMe" value="1"></td></tr>
        <tr><td><input type="radio" name="testMe" value="2"></td></tr>
        <tr><td><input type="radio" name="testMe" value="3"></td></tr>
        <tr><td><input type="radio" name="testMe" value="4"></td></tr>
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

    public function testRadioOptions(): void
    {
        GridView::counter(0);

        $columns = [
            [
                '__class' => RadioButtonColumn::class,
                'radioOptions()' => [['class' => 'testMe']],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>&nbsp;</th></tr>
        </thead>
        <tbody>
        <tr><td><input type="radio" class="testMe" name="radioButtonSelection" value="0"></td></tr>
        <tr><td><input type="radio" class="testMe" name="radioButtonSelection" value="1"></td></tr>
        <tr><td><input type="radio" class="testMe" name="radioButtonSelection" value="2"></td></tr>
        <tr><td><input type="radio" class="testMe" name="radioButtonSelection" value="3"></td></tr>
        <tr><td><input type="radio" class="testMe" name="radioButtonSelection" value="4"></td></tr>
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

        $columns = [
            'id',
            'name',
            'total',
            [
                '__class' => RadioButtonColumn::class,
                'radioOptions' => static fn ($model) => ['value' => $model['total'] > 40 ? 1 : 0],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w2-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>Id</th><th>Name</th><th>Total</th><th>&nbsp;</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td><td>tests 1</td><td>10</td><td><input type="radio" name="radioButtonSelection" value="0"></td></tr>
        <tr><td>2</td><td>tests 2</td><td>20</td><td><input type="radio" name="radioButtonSelection" value="0"></td></tr>
        <tr><td>3</td><td>tests 3</td><td>30</td><td><input type="radio" name="radioButtonSelection" value="0"></td></tr>
        <tr><td>4</td><td>tests 4</td><td>40</td><td><input type="radio" name="radioButtonSelection" value="0"></td></tr>
        <tr><td>5</td><td>tests 5</td><td>50</td><td><input type="radio" name="radioButtonSelection" value="1"></td></tr>
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

    public function testNameIsEmpty(): void
    {
        $gridView = $this->createGridView([['__class' => RadioButtonColumn::class,'name()' => ['']]]);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "name" property it cannot be empty.');

        $gridView->render();
    }

    public function testValue(): void
    {
        $column = $this->radioButtonColumn;

        $html = <<<'HTML'
        <td><input type="radio" name="radioButtonSelection" value="1"></td>
        HTML;
        $this->assertSame($html, $column->renderDataCell([], 1, 0));

        $html = <<<'HTML'
        <td><input type="radio" name="radioButtonSelection" value="42"></td>
        HTML;
        $this->assertSame($html, $column->renderDataCell([], 42, 0));

        $html = <<<'HTML'
        <td><input type="radio" name="radioButtonSelection" value="[1,42]"></td>
        HTML;
        $this->assertSame($html, $column->renderDataCell([], [1,42], 0));

        $column = $this->radioButtonColumn->radioOptions(['value' => 42])->grid($this->createGridView());
        $this->assertStringNotContainsString('value="1"', $column->renderDataCell([], 1, 0));

        $html = <<<'HTML'
        <td><input type="radio" name="radioButtonSelection" value="42"></td>
        HTML;
        $this->assertSame($html, $column->renderDataCell([], 1, 0));
    }
}
