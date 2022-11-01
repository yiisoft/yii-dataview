<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\RadioColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()
                        ->content(
                            static fn (array $data, mixed $key, int $index): string => '<input name="radio-selection" type="radio" value="' . $index . '">'
                        ),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()
                        ->content(
                            static fn (array $data, mixed $key, int $index): string => '<input name="radio-selection" type="radio" value="' . $index . '">'
                        )
                        ->contentAttributes(['class' => 'test-class']),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->dataLabel('test.label'),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->label('test.label'),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->label('Ραδιόφωνο'),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->label('test.label')->labelAttributes(['class' => 'test-class']),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->name('test.radio'),
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
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create()->visible(false),
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
            <div>dataview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    DataColumn::create()->attribute('id'),
                    DataColumn::create()->attribute('name'),
                    RadioColumn::create(),
                )
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }
}
