<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
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

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
            <div class="testMe">
            <tr>
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            </div>
            <div class="testMe">
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </div>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->afterRow(static fn () => '</div>')
                ->beforeRow(static fn () => '<div class="testMe">')
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
                )
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create()->attributes(['class' => 'text-primary']),
                    DataColumn::create()->attribute('id')->attributes(['class' => 'bg-primary']),
                    DataColumn::create()->attribute('name')->attributes(['class' => 'bg-success']),
                )
                ->columnsGroupEnabled(true)
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
                )
                ->columnsGroupEnabled(true)
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
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            <td data-label="actions">
            <a name="view" href="/admin/manage/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/manage/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/manage/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
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

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
            <td data-label="id">Empty cell</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(DataColumn::create()->attribute('id'))
                ->emptyCell('Empty cell')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator([['id' => '']], 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
            <td>Total:</td><td>2</td><td>2</td>
            </tr>
            </tfoot>
            <tbody>
            <tr>
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create()->footer('Total:'),
                    DataColumn::create()->attribute('id')->footer('2'),
                    DataColumn::create()->attribute('name')->footer('2'),
                )
                ->footerEnabled(true)
                ->footerRowAttributes(['class' => 'text-primary'])
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
    public function testHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>List of users</div>
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
                )
                ->header('List of users')
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
                )
                ->header('List of users')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->layout('')
                ->layoutGridTable("{header}\n{items}\n{summary}")
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr class="text-primary">
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr class="text-primary">
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
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
            <td data-label="#">1</td>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="age">20</td>
            </tr>
            <tr>
            <td data-label="#">2</td>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    SerialColumn::create(),
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('age'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->tableAttributes(['class' => 'table table-striped table-bordered'])
                ->render()
        );
    }
}
