<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class DataColumnFilterTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    private array $dataWithDate = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'birthday' => '2000-01-01'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'birthday' => '2000-01-02'],
    ];

    private array $dataWithDateTime = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'birthday' => '2000-01-01 00:00:00'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'birthday' => '2000-01-02 00:00:00'],
    ];

    private array $dataWithEmail = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'email' => 'test1@example.com'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'email' => 'test2@example.com'],
    ];

    private array $dataWithMonth = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'month' => '2000-01'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'month' => '2000-02'],
    ];

    private array $dataWithTelephone = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'telephone' => '1 (555) 123-4567'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'telephone' => '1 (555) 123-4568'],
    ];

    private array $dataWithTime = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'time' => '12:00:00'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'time' => '12:00:01'],
    ];

    private array $dataWithUrl = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'url' => 'http://example.com'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'url' => 'http://example.org'],
    ];

    private array $dataWithWeek = [
        ['id' => 1, 'name' => 'John', 'age' => 20, 'week' => '2000-W01'],
        ['id' => 2, 'name' => 'Mary', 'age' => 21, 'week' => '2000-W02'],
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilter(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td><input name="searchModel[name]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn('id', filter: '&nbsp;'),
                    new DataColumn('name', filter: '<input name="searchModel[name]">'),
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
    public function testFilterDate(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Birthday</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="date" name="birthday"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>2000-01-01</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>2000-01-02</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('birthday', filterProperty: 'birthday', filterType: 'date'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithDate, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterDateTime(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Birthday</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="datetime-local" name="birthday"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>2000-01-01 00:00:00</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>2000-01-02 00:00:00</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('birthday', filterProperty: 'birthday', filterType: 'datetime'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithDateTime, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterEmail(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="email" name="email"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>test1@example.com</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>test2@example.com</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('email', filterProperty: 'email', filterType: 'email'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithEmail, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterInputAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td><input type="text" class="test.class" name="searchModel[id]" value="0"></td>
            <td><input type="text" class="test.class" name="searchModel[name]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterInputAttributes: ['class' => 'test.class'],
                        filterValueDefault: 0,
                    ),
                    new DataColumn(
                        property: 'name',
                        filterProperty: 'name',
                        filterInputAttributes: ['class' => 'test.class'],
                        filterValueDefault: '',
                    ),
                )
                ->filterModelName('searchModel')
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
    public function testFilterMonth(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Month</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="month" name="month"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>2000-01</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>2000-02</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('month', filterProperty: 'month', filterType: 'month'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithMonth, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterNumber(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td><input type="number" name="id"></td>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn('id', filterProperty: 'id', filterType: 'number'),
                    new DataColumn('name'),
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
    public function testFilterPositionFooter(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
            <tr>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></td>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterValueDefault: 0,
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                    new DataColumn(
                        property: 'name',
                        filterProperty: 'name',
                        filterValueDefault: '',
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                )
                ->filterModelName('searchModel')
                ->filterPosition(GridView::FILTER_POS_FOOTER)
                ->footerEnabled(true)
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
    public function testFilterPositionHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></td>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></td>
            </tr>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterValueDefault: 0,
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                    new DataColumn(
                        property: 'name',
                        filterProperty: 'name',
                        filterValueDefault: '',
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                )
                ->filterModelName('searchModel')
                ->filterPosition(GridView::FILTER_POS_HEADER)
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
    public function testFilterRange(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td><input type="range" name="id" value="0"></td>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterType: 'range',
                        filterValueDefault: 0,
                    ),
                    new DataColumn('name'),
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
    public function testFilterRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="text-center">
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></td>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterValueDefault: 0,
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                    new DataColumn(
                        property: 'name',
                        filterProperty: 'name',
                        filterValueDefault: '',
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
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
    public function testFilterSearch(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td><input type="search" name="name"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn('id'),
                    new DataColumn('name', filterProperty: 'name', filterType: 'search'),
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
    public function testFilterSelect(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="text-center">
            <td><select name="searchModel[id]">
            <option value>Select...</option>
            <option value="1">Jhon</option>
            <option value="2">Mary</option>
            </select></td>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterInputSelectItems: ['1' => 'Jhon', '2' => 'Mary'],
                        filterInputSelectPrompt: 'Select...',
                        filterValueDefault: 0,
                        filterType: 'select',
                    ),
                    new DataColumn('name'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
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
    public function testFilterTelephone(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Telephone</th>
            </tr>
            <tr class="text-center">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="tel" name="searchModel[telephone]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>1 (555) 123-4567</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>1 (555) 123-4568</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('telephone', filterProperty: 'telephone', filterType: 'tel'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithTelephone, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterTime(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Time</th>
            </tr>
            <tr class="text-center">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="time" name="searchModel[time]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>12:00:00</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>12:00:01</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('time', filterProperty: 'time', filterType: 'time'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithTime, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterUrl(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Url</th>
            </tr>
            <tr class="text-center">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="url" name="searchModel[url]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>&nbsp;</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>&nbsp;</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('url', filterProperty: 'url', filterType: 'url'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithTime, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilterWeek(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Week</th>
            </tr>
            <tr class="text-center">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="week" name="searchModel[week]"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            <td>2000-W01</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Mary</td>
            <td>2000-W02</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name'),
                    new DataColumn('week', filterProperty: 'week', filterType: 'week'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->dataWithWeek, 10))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testFilters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></td>
            <td class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>John</td>
            </tr>
            <tr>
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
                    new DataColumn(
                        property: 'id',
                        filterProperty: 'id',
                        filterValueDefault: 0,
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                    new DataColumn(
                        property: 'name',
                        filterProperty: 'name',
                        filterValueDefault: '',
                        filterAttributes: ['class' => 'text-center', 'style' => 'width:60px'],
                        filterInputAttributes: ['maxlength' => '5'],
                    ),
                )
                ->filterModelName('searchModel')
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }
}
