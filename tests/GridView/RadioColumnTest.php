<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\RadioColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class RadioColumnTest extends TestCase
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
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td><input name="radio-selection" type="radio" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td><input name="radio-selection" type="radio" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContent())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td class="test-class"><input name="radio-selection" type="radio" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td class="test-class"><input name="radio-selection" type="radio" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContentAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="test.label"><input type="radio" name="radio-selection" value="0" data-label="test.label"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="radio" name="radio-selection" value="1" data-label="test.label"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithDataLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <td data-label="test.label"><input type="radio" name="radio-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="radio" name="radio-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th>Ραδιόφωνο</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="ραδιόφωνο"><input type="radio" name="radio-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="ραδιόφωνο"><input type="radio" name="radio-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelMbString())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th class="test-class">test.label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td data-label="test.label"><input type="radio" name="radio-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="test.label"><input type="radio" name="radio-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td name="test.radio"><input type="radio" name="test.radio" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td name="test.radio"><input type="radio" name="test.radio" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithName())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithNotVisible())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
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
            <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">John</td>
            <td><input type="radio" name="radio-selection" value="0"></td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td><input type="radio" name="radio-selection" value="1"></td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
                ->render()
        );
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumns(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create(),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithContent(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->content(
                static fn (array $data, mixed $key, int $index): string => '<input name="radio-selection" type="radio" value="' . $index . '">'
            ),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithContentAttributes(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()
                ->content(
                    static fn (array $data, mixed $key, int $index): string => '<input name="radio-selection" type="radio" value="' . $index . '">'
                )
                ->contentAttributes(['class' => 'test-class']),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithDataLabel(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->dataLabel('test.label'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithLabel(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->label('test.label'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithLabelMbString(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->label('Ραδιόφωνο'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithLabelAttributes(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->label('test.label')->labelAttributes(['class' => 'test-class']),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithName(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->name('test.radio'),
        ];
    }

    /**
     * @psalm-return array<DataColumn|RadioColumn>
     */
    private function createColumnsWithNotVisible(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
            RadioColumn::create()->visible(false),
        ];
    }
}
