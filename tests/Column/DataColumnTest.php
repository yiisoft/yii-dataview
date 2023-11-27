<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class DataColumnTest extends TestCase
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
            <table>
            <thead>
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
                        'id',
                        content: static fn(array $data): int => (int)$data['id'],
                    ),
                    new DataColumn(
                        'name',
                        content: static fn(array $data): string => (string)$data['name'],
                    ),
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
    public function testContentAttributes(): void
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
            <tbody>
            <tr>
            <td class="test.class">1</td>
            <td class="test.class">John</td>
            </tr>
            <tr>
            <td class="test.class">2</td>
            <td class="test.class">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn(
                        'id',
                        content: static fn(array $data): int => (int)$data['id'],
                        bodyAttributes: ['class' => 'test.class'],
                    ),
                    new DataColumn(
                        'name',
                        content: static fn(array $data): string => (string)$data['name'],
                        bodyAttributes: ['class' => 'test.class'],
                    ),
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
    public function testContentAttributesClosure(): void
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
            <tbody>
            <tr>
            <td class="test.class">1</td>
            <td class="test.class">John</td>
            </tr>
            <tr>
            <td class="test.class">2</td>
            <td class="test.class">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id', bodyAttributes: ['class' => static fn(): string => 'test.class']),
                    new DataColumn('name', bodyAttributes: ['class' => static fn(): string => 'test.class']),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>test.id</th>
            <th>test.username</th>
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
                    new DataColumn('id', header: 'test.id'),
                    new DataColumn('name', header: 'test.username'),
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
    public function testLabelMbString(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th>Όνομα χρήστη</th>
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
                    new DataColumn('name', header: 'Όνομα χρήστη'),
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
    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th class="test.class">test.id</th>
            <th class="test.class">test.username</th>
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
                    new DataColumn('id', header: 'test.id', headerAttributes: ['class' => 'test.class']),
                    new DataColumn('name', header: 'test.username', headerAttributes: ['class' => 'test.class']),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }

    public function testNotSorting(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th>Id</th>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>test</td>
            </tr>
            <tr>
            <td>2</td>
            <td>test</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id', withSorting: false),
                    new DataColumn('name', content: 'test'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10, 1, true))
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
            <table>
            <thead>
            <tr>
            <th>Id</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            </tr>
            <tr>
            <td>2</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id'),
                    new DataColumn('name', visible: false),
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
    public function testSort(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
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
                    new DataColumn('name'),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10, 1, true))
                ->render()
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testValue(): void
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
            <tbody>
            <tr>
            <td>1</td>
            <td>test</td>
            </tr>
            <tr>
            <td>1</td>
            <td>test</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn('id', content: 1),
                    new DataColumn('name', content: 'test'),
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
    public function testValueClosure(): void
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
                        'id',
                        content: static fn(array $data): string => (string)$data['id']
                    ),
                    new DataColumn(
                        'name',
                        content: static fn(array $data): string => (string)$data['name']
                    ),
                )
                ->id('w1-grid')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render()
        );
    }
}
