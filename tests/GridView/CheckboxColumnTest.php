<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class CheckboxColumnTest extends TestCase
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
    public function testContent(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th><input type="checkbox" class="select-on-check-all" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td><input name="checkbox-selection" type="checkbox" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td><input name="checkbox-selection" type="checkbox" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContent())
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
    public function testContentAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th><input type="checkbox" class="select-on-check-all" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td class="test.class"><input name="checkbox-selection" type="checkbox" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td class="test.class"><input name="checkbox-selection" type="checkbox" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContentAttributes())
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
    public function testDataLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th><input type="checkbox" class="select-on-check-all" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="0" data-label="test.label"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="1" data-label="test.label"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithDataLabel())
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
    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabel())
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
    public function testLabelMbString(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Πλαίσιο ελέγχου</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="πλαίσιο ελέγχου"><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="πλαίσιο ελέγχου"><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelMbString())
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
    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th class="test.class">test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelAttributes())
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
    public function testName(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th><input type="checkbox" class="select-on-check-all" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td name="test.checkbox"><input type="checkbox" name="test.checkbox" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td name="test.checkbox"><input type="checkbox" name="test.checkbox" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithName())
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
    public function testNotMultiple(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithNotMultiple())
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
    public function testNotVisible(): void
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
                ->columns($this->createColumnsWithNotVisible())
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
    public function testRender(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Name</th>
            <th><input type="checkbox" class="select-on-check-all" name="checkbox-selection-all" value="1"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td><input type="checkbox" name="checkbox-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td><input type="checkbox" name="checkbox-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumns(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create(),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithContent(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->content(
                static fn (array|object $data, mixed $key, int $index): string => '<input name="checkbox-selection" type="checkbox" value="' . $index . '">'
            ),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithContentAttributes(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()
                ->content(
                    static fn (array|object $data, mixed $key, int $index): string => '<input name="checkbox-selection" type="checkbox" value="' . $index . '">'
                )
                ->contentAttributes(['class' => 'test.class']),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithDataLabel(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->dataLabel('test.label'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithLabel(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->label('test.label'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithLabelMbString(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->label('Πλαίσιο ελέγχου'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithLabelAttributes(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->label('test.label')->labelAttributes(['class' => 'test.class']),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithName(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->name('test.checkbox'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithNotMultiple(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->multiple(false),
        ];
    }

    /**
     * @psalm-return array<DataColumn|CheckboxColumn>
     */
    private function createColumnsWithNotVisible(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            CheckboxColumn::create()->visible(false),
        ];
    }
}
