<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\SerialColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\ActionButton;

final class BaseTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    public function testAfterItemBeforeItem(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr class="before"></tr>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr class="after"></tr>
            <tr class="before"></tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            <tr class="after"></tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->afterRow(static fn() => Html::tr(['class' => 'after']))
                ->beforeRow(static fn() => Html::tr(['class' => 'before']))
                ->columns(
                    new SerialColumn(),
                    new DataColumn(property: 'id'),
                    new DataColumn(property: 'name'),
                    new DataColumn(property: 'age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testColumnGroupEnabled(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <colgroup>
            <col class="text-primary">
            <col class="bg-primary">
            <col class="bg-success">
            </colgroup>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(columnAttributes: ['class' => 'text-primary']),
                    new DataColumn(property: 'id', columnAttributes: ['class' => 'bg-primary']),
                    new DataColumn(property: 'name', columnAttributes: ['class' => 'bg-success']),
                )
                ->columnGrouping()
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testColumnGroupEnabledEmpty(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <colgroup>
            <col>
            <col>
            <col>
            <col>
            </colgroup>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn(property: 'id'),
                    new DataColumn(property: 'name'),
                    new DataColumn(property: 'age'),
                )
                ->columnGrouping()
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testEmptyCell(): void
    {
        $attributes = ['class' => 'empty-cell'];

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td class="empty-cell">Empty cell</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new DataColumn('id'))
                ->emptyCell('Empty cell', $attributes)
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([['id' => '']], 10))
                ->render()
        );
    }

    public function testEmptyText(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="4">Not found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->emptyText('Not found.')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFooterRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="text-primary">
            <td>Total:</td>
            <td>2</td>
            <td>2</td>
            </tr>
            </tfoot>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(footer: 'Total:'),
                    new DataColumn('id', footer: '2'),
                    new DataColumn('name', footer: '2'),
                )
                ->enableFooter(true)
                ->footerRowAttributes(['class' => 'text-primary'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <div>List of users</div>

            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->header('List of users')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testHeaderIntoGrid(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <div>List of users</div>
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->header('List of users')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->layout("{header}\n{items}\n{summary}")
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testHeaderRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr class="text-primary">
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->headerRowAttributes(['class' => 'text-primary'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testHeaderTableEnabledFalse(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->enableHeader(false)
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderEmptyData(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="4">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-primary">
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr class="text-primary">
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->bodyRowAttributes(['class' => 'text-primary'])
                ->render()
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-primary">
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr class="text-primary">
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->bodyRowAttributes(['class' => 'text-primary'])
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testTableAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table table-striped table-bordered">
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            </tr>
            <tr>
            <td>2</td>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new SerialColumn(),
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->tableAttributes(['class' => 'table table-striped table-bordered'])
                ->render()
        );
    }

    public function testNoSortInUrlWhenLimited(): void
    {
        $sort = Sort::any()->withOrder(['id' => 'asc', 'name' => 'asc']);
        $data = (new IterableDataReader($this->data))
            ->withSort($sort)
            ->withLimit(10);

        $paginator = (new OffsetPaginator($data))
            ->withPageSize(1);

        $output = GridView::widget()
            ->columns(
                new SerialColumn(),
                new DataColumn('id'),
                new DataColumn('name'),
                new DataColumn('age'),
            )
            ->id('w1-grid')
            ->dataReader($paginator)
            ->tableAttributes(['class' => 'table table-striped table-bordered'])
            ->render();

        $this->assertStringNotContainsString('sort=', $output);
    }

    public function testEmptyCellAttributes(): void
    {
        $attributes = ['class' => 'empty-cell', 'data-test' => 'test-value'];

        $html = GridView::widget()
            ->id('w1-grid')
            ->emptyCellAttributes($attributes)
            ->columns(
                new SerialColumn(),
                new DataColumn('id'),
                new DataColumn('name'),
                new DataColumn('age'),
            )
            ->dataReader($this->createOffsetPaginator([
                [
                    'id' => 1,
                    'name' => '',
                    'age' => 42,
                ],
            ], 10))
            ->render();

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>#</th>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>1</td>
            <td class="empty-cell" data-test="test-value">&nbsp;</td>
            <td>42</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            $html
        );
    }

    public function testAddTableClass(): void
    {
        $reader = new IterableDataReader($this->data);
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            );

        $newGridView = $gridView->addTableClass('test-class', 'another-class');

        // Assert original instance not modified
        $this->assertStringNotContainsString('test-class', $gridView->render());

        // Assert new instance has classes
        $html = $newGridView->render();
        $this->assertStringContainsString('test-class', $html);
        $this->assertStringContainsString('another-class', $html);
    }

    public function testTableClass(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            )
            ->addTableClass('initial-class');

        $newGridView = $gridView->tableClass('test-class', 'another-class', null);

        // Assert original instance still has initial class
        $html = $gridView->render();
        $this->assertStringContainsString('initial-class', $html);
        $this->assertStringNotContainsString('test-class', $html);

        // Assert new instance has new classes and old class is removed
        $html = $newGridView->render();
        $this->assertStringContainsString('test-class', $html);
        $this->assertStringContainsString('another-class', $html);
        $this->assertStringNotContainsString('initial-class', $html);
    }

    public function testTbodyAttributes(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            );

        $newGridView = $gridView->tbodyAttributes([
            'class' => 'tbody-class',
            'data-test' => 'test-value',
            'id' => 'test-tbody',
        ]);

        // Assert original instance doesn't have attributes
        $html = $gridView->render();
        $this->assertStringNotContainsString('tbody-class', $html);
        $this->assertStringNotContainsString('data-test', $html);
        $this->assertStringNotContainsString('test-tbody', $html);

        // Assert new instance has all attributes
        $html = $newGridView->render();
        $this->assertStringContainsString('tbody-class', $html);
        $this->assertStringContainsString('data-test="test-value"', $html);
        $this->assertStringContainsString('id="test-tbody"', $html);
    }

    public function testAddTbodyClass(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            )
            ->tbodyAttributes(['class' => 'initial-class']);

        $newGridView = $gridView->addTbodyClass('test-class', 'another-class', null);

        // Assert original instance still has only initial class
        $html = $gridView->render();
        $this->assertStringContainsString('initial-class', $html);
        $this->assertStringNotContainsString('test-class', $html);
        $this->assertStringNotContainsString('another-class', $html);

        // Assert new instance has all classes including the initial one
        $html = $newGridView->render();
        $this->assertStringContainsString('initial-class', $html);
        $this->assertStringContainsString('test-class', $html);
        $this->assertStringContainsString('another-class', $html);
    }

    public function testTbodyClass(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            )
            ->tbodyAttributes(['class' => 'initial-class']);

        $newGridView = $gridView->tbodyClass('test-class', 'another-class', null);

        // Assert original instance still has initial class
        $html = $gridView->render();
        $this->assertStringContainsString('initial-class', $html);
        $this->assertStringNotContainsString('test-class', $html);
        $this->assertStringNotContainsString('another-class', $html);

        // Assert new instance has new classes and old class is removed
        $html = $newGridView->render();
        $this->assertStringContainsString('test-class', $html);
        $this->assertStringContainsString('another-class', $html);
        $this->assertStringNotContainsString('initial-class', $html);
    }

    public function testHeaderCellAttributes(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            );

        $newGridView = $gridView->headerCellAttributes([
            'class' => 'header-class',
            'data-test' => 'test-value',
            'scope' => 'col',
        ]);

        // Assert original instance doesn't have attributes
        $html = $gridView->render();
        $this->assertStringNotContainsString('header-class', $html);
        $this->assertStringNotContainsString('data-test="test-value"', $html);
        $this->assertStringNotContainsString('scope="col"', $html);

        // Assert new instance has all attributes
        $html = $newGridView->render();
        $this->assertStringContainsString('header-class', $html);
        $this->assertStringContainsString('data-test="test-value"', $html);
        $this->assertStringContainsString('scope="col"', $html);
    }

    public function testBodyCellAttributes(): void
    {
        $paginator = $this->createOffsetPaginator($this->data, 10);

        $gridView = GridView::widget()
            ->dataReader($paginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name')
            );

        $newGridView = $gridView->bodyCellAttributes([
            'class' => 'cell-class',
            'data-test' => 'test-value',
            'title' => 'Cell title',
        ]);

        // Assert original instance doesn't have attributes
        $html = $gridView->render();
        $this->assertStringNotContainsString('cell-class', $html);
        $this->assertStringNotContainsString('data-test="test-value"', $html);
        $this->assertStringNotContainsString('title="Cell title"', $html);

        // Assert new instance has all attributes
        $html = $newGridView->render();
        $this->assertStringContainsString('cell-class', $html);
        $this->assertStringContainsString('data-test="test-value"', $html);
        $this->assertStringContainsString('title="Cell title"', $html);
    }

    public function testFilterFormRendersCustomAttributesAndAssignsFormAttributeToInput(): void
    {
        $html = GridView::widget()
            ->columns(
                new DataColumn('id', filter: true),
            )
            ->filterFormAttributes(['data-testid' => 'filter-form', 'style' => 'opacity:1'])
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator([
                ['id' => 1],
                ['id' => 2],
            ], 10))
            ->render();

        $this->assertStringContainsString('data-testid="filter-form"', $html);
        $this->assertStringContainsString('style="opacity: 1; display: none;"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+name="id"[^>]+form="/', $html);
    }

    public function testBodyRowAttributesCallbackAddsCustomAttributesToRow(): void
    {
        $html = GridView::widget()
            ->columns(new DataColumn('id'))
            ->bodyRowAttributes(fn($data, $context) => ['data-id' => $data['id']])
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator([['id' => 123]], 10))
            ->render();

        $this->assertStringContainsString('data-id="123"', $html);
    }

    public function testCallableAndStaticRowAttributesAppliedFromArray(): void
    {
        $html = GridView::widget()
            ->columns(new DataColumn('id'))
            ->bodyRowAttributes([
                'data-id' => fn($data, $context) => $data['id'],
                'class' => 'row-class',
            ])
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator([['id' => 456]], 10))
            ->render();

        $this->assertStringContainsString('data-id="456"', $html);
        $this->assertStringContainsString('class="row-class"', $html);
    }

    public function testKeysetPaginationLinkClassRenderedInGridView(): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
        ];

        $sort = Sort::only(['id'])->withOrder(['id' => 'asc']);

        $reader = (new IterableDataReader($data))
            ->withSort($sort);

        $paginator = (new KeysetPaginator($reader))
            ->withToken(PageToken::next('2'));

        $html = GridView::widget()
            ->columns(new DataColumn('id'))
            ->dataReader($paginator)
            ->keysetPaginationConfig([
                'linkClass()' => ['test-link'],
            ])
            ->id('w1-grid')
            ->render();

        $this->assertStringContainsString('class="test-link"', $html);
    }

    public function testKeysetPaginationRendersCombinedLinkClassesInGridView(): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
        ];

        $sort = Sort::only(['id'])->withOrder(['id' => 'asc']);

        $reader = (new IterableDataReader($data))
            ->withSort($sort);

        $paginator = (new KeysetPaginator($reader))
            ->withToken(PageToken::next('2'));

        $html = GridView::widget()
            ->columns(new DataColumn('id'))
            ->dataReader($paginator)
            ->keysetPaginationConfig([
                'linkClass()' => ['base'],
                'addLinkClass()' => ['extra', 'highlight'],
            ])
            ->id('w1-grid')
            ->render();

        $this->assertStringContainsString('class="base extra highlight"', $html);
    }

    public function testOffsetPaginationRendersCombinedLinkAttributesInGridView(): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
        ];

        $reader = new IterableDataReader($data);

        $paginator = (new OffsetPaginator($reader))
            ->withPageSize(1)
            ->withCurrentPage(1);

        $html = GridView::widget()
            ->columns(new DataColumn('id'))
            ->dataReader($paginator)
            ->offsetPaginationConfig([
                'linkAttributes()' => [['data-base' => 'base']],
                'addLinkAttributes()' => [['data-added' => 'yes', 'class' => 'custom-class']],
            ])
            ->id('w1-grid')
            ->render();

        $this->assertStringContainsString('data-base="base"', $html);
        $this->assertStringContainsString('data-added="yes"', $html);
        $this->assertStringContainsString('class="custom-class"', $html);
    }

    public function testActionColumnAndCustomButtonAttributesRenderedInGridView(): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
        ];

        $paginator = $this->createOffsetPaginator($data, 10);

        $html = GridView::widget()
            ->columns(
                new ActionColumn(
                    columnAttributes: ['class' => 'action-col'],
                    buttons: [
                        'custom' => new ActionButton(
                            content: '🧪',
                            url: static fn(array $data) => '/test?id=' . $data['id'],
                            attributes: ['data-testid' => 'custom-button'],
                            class: 'custom-class',
                            title: 'Custom action'
                        ),
                    ],
                    template: '{custom}',
                    visibleButtons: ['custom' => true],
                )
            )
            ->dataReader($paginator)
            ->id('w1-grid')
            ->columnGrouping(true)
            ->render();

        $this->assertStringContainsString('<col class="action-col">', $html);
        $this->assertStringContainsString('title="Custom action"', $html);
        $this->assertStringContainsString('/test?id=1', $html);
        $this->assertStringContainsString('🧪', $html);
        $this->assertStringContainsString('class="custom-class"', $html);
        $this->assertStringContainsString('data-testid="custom-button"', $html);
    }
}
