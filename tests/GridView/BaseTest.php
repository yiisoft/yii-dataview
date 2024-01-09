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
                ->columnsGroupEnabled(true)
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
                ->columnsGroupEnabled(true)
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testColumnGuess(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Age</th>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>20</td>
            <td>
            <a href="/admin/manage/view?id=1" title="View">🔎</a>
            <a href="/admin/manage/update?id=1" title="Update">✎</a>
            <a href="/admin/manage/delete?id=1" title="Delete">❌</a>
            </td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>21</td>
            <td>
            <a href="/admin/manage/view?id=2" title="View">🔎</a>
            <a href="/admin/manage/update?id=2" title="Update">✎</a>
            <a href="/admin/manage/delete?id=2" title="Delete">❌</a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testEmptyCell(): void
    {
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
            <td>Empty cell</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(new DataColumn('id'))
                ->emptyCell('Empty cell')
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
                ->footerEnabled(true)
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
                ->headerTableEnabled(false)
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
                ->rowAttributes(['class' => 'text-primary'])
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
                ->rowAttributes(['class' => 'text-primary'])
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
}
