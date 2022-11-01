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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <th>&nbsp;</th>
            <th><input name="searchModel[name]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id')->filter('&nbsp;'),
                    DataColumn::create()->attribute('name')->filter('<input name="searchModel[name]">'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Birthday</th>
            </tr>
            <tr class="filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="date" name="birthday"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="birthday">2000-01-01</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="birthday">2000-01-02</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('birthday')->filterAttribute('birthday')->filterType('date'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithDate, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Birthday</th>
            </tr>
            <tr class="filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="datetime-local" name="birthday"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="birthday">2000-01-01 00:00:00</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="birthday">2000-01-02 00:00:00</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('birthday')->filterAttribute('birthday')->filterType('datetime'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithDateTime, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
            </tr>
            <tr class="filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="email" name="email"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="email">test1@example.com</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="email">test2@example.com</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('email')->filterAttribute('email')->filterType('email'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithEmail, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <th><input type="text" class="test.class" name="searchModel[id]" value="0"></th>
            <th><input type="text" class="test.class" name="searchModel[name]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterInputAttributes(['class' => 'test.class'])
                        ->filterValueDefault(0),
                    DataColumn::create()
                        ->attribute('name')
                        ->filterAttribute('name')
                        ->filterInputAttributes(['class' => 'test.class'])
                        ->filterValueDefault(''),
                )
                ->filterModelName('searchModel')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Month</th>
            </tr>
            <tr class="filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="month" name="month"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="month">2000-01</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="month">2000-02</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('month')->filterAttribute('month')->filterType('month'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithMonth, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <th><input type="number" name="id"></th>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id')->filterAttribute('id')->filterType('number'),
                    DataColumn::create()->attribute('name'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
            <td>&nbsp;</td><td>&nbsp;</td>
            </tr>
            <tr class="filters">
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></th>
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></th>
            </tr>
            </tfoot>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterValueDefault(0)
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                    DataColumn::create()
                        ->attribute('name')
                        ->filterAttribute('name')
                        ->filterValueDefault('')
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                )
                ->filterModelName('searchModel')
                ->filterPosition(GridView::FILTER_POS_FOOTER)
                ->footerEnabled(true)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr class="filters">
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></th>
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></th>
            </tr>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterValueDefault(0)
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                    DataColumn::create()
                        ->attribute('name')
                        ->filterAttribute('name')
                        ->filterValueDefault('')
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                )
                ->filterModelName('searchModel')
                ->filterPosition(GridView::FILTER_POS_HEADER)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <th><input type="range" name="id" value="0"></th>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterType('range')
                        ->filterValueDefault(0),
                    DataColumn::create()->attribute('name'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="text-center filters">
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></th>
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterValueDefault(0)
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                    DataColumn::create()
                        ->attribute('name')
                        ->filterAttribute('name')
                        ->filterValueDefault('')
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <td>&nbsp;</td>
            <th><input type="search" name="name"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name')->filterAttribute('name')->filterType('search'),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="text-center filters">
            <th><select name="searchModel[id]">
            <option value>Select...</option>
            <option value="1">Jhon</option>
            <option value="2">Mary</option>
            </select></th>
            <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterInputSelectItems(['1' => 'Jhon', '2' => 'Mary'])
                        ->filterInputSelectPrompt('Select...', 0)
                        ->filterType('select'),
                    DataColumn::create()->attribute('name'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Telephone</th>
            </tr>
            <tr class="text-center filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="tel" name="searchModel[telephone]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="telephone">1 (555) 123-4567</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="telephone">1 (555) 123-4568</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('telephone')->filterAttribute('telephone')->filterType('tel'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithTelephone, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Time</th>
            </tr>
            <tr class="text-center filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="time" name="searchModel[time]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="time">12:00:00</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="time">12:00:01</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('time')->filterAttribute('time')->filterType('time'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithTime, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Url</th>
            </tr>
            <tr class="text-center filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="url" name="searchModel[url]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="url">&nbsp;</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="url">&nbsp;</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('url')->filterAttribute('url')->filterType('url'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithTime, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Week</th>
            </tr>
            <tr class="text-center filters">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <th><input type="week" name="searchModel[week]"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="week">2000-W01</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="week">2000-W02</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    DataColumn::create()->attribute('week')->filterAttribute('week')->filterType('week'),
                )
                ->filterModelName('searchModel')
                ->filterRowAttributes(['class' => 'text-center'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->dataWithWeek, 10))
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
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            </tr>
            <tr class="filters">
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[id]" value="0" maxlength="5"></th>
            <th class="text-center" style="width:60px"><input type="text" name="searchModel[name]" maxlength="5"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()
                        ->attribute('id')
                        ->filterAttribute('id')
                        ->filterValueDefault(0)
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                    DataColumn::create()
                        ->attribute('name')
                        ->filterAttribute('name')
                        ->filterValueDefault('')
                        ->filterAttributes(['class' => 'text-center', 'style' => 'width:60px'])
                        ->filterInputAttributes(['maxlength' => '5']),
                )
                ->filterModelName('searchModel')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }
}
