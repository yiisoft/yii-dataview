<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class DataColumnTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

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
                ->columns($this->createColumnsWithContent())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

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
            </tr>
            </thead>
            <tbody>
            <tr>
            <td class="test.class" data-label="id">1</td>
            <td class="test.class" data-label="name">John</td>
            </tr>
            <tr>
            <td class="test.class" data-label="id">2</td>
            <td class="test.class" data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContentAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testContentAttributesClosure(): void
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
            <td class="test.class" data-label="id">1</td>
            <td class="test.class" data-label="name">John</td>
            </tr>
            <tr>
            <td class="test.class" data-label="id">2</td>
            <td class="test.class" data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithContentAttributesClosure())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

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
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.id">1</td>
            <td data-label="test.name">John</td>
            </tr>
            <tr>
            <td data-label="test.id">2</td>
            <td data-label="test.name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithDataLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testLabel(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>test.id</th>
            <th>test.username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.id">1</td>
            <td data-label="test.username">John</td>
            </tr>
            <tr>
            <td data-label="test.id">2</td>
            <td data-label="test.username">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabel())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testLabelMbString(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th>Όνομα χρήστη</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="όνομα χρήστη">John</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="όνομα χρήστη">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelMbString())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testLabelAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th class="test.class">test.id</th>
            <th class="test.class">test.username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="test.id">1</td>
            <td data-label="test.username">John</td>
            </tr>
            <tr>
            <td data-label="test.id">2</td>
            <td data-label="test.username">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithLabelAttributes())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testLinkSorter(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a href="/admin/manage/1/5?sort=id" data-sort="id">id</a></th>
            <th><a href="/admin/manage/1/5?sort=name" data-sort="name">name</a></th>
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
                ->columns($this->createColumnsWithLinkSorter())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testName(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td name="test.id" data-label="id">1</td>
            <td name="test.username" data-label="name">John</td>
            </tr>
            <tr>
            <td name="test.id" data-label="id">2</td>
            <td name="test.username" data-label="name">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithName())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1, true))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->urlName('admin/manage')
                ->render()
        );
    }

    public function testNotSorting(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">test</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">test</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithNotSorting())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1, true))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->urlName('admin/manage')
                ->render()
        );
    }

    public function testNotVisible(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th>Id</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td data-label="id">1</td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithNotVisible())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->urlName('admin/manage')
                ->render()
        );
    }

    public function testSort(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-id%2Cname" data-sort="-id,name">Id <i class="bi bi-sort-alpha-up"></i></a></th>
            <th><a class="asc" href="/admin/manage?page=1&amp;pagesize=10&amp;sort=-name%2Cid" data-sort="-name,id">Name <i class="bi bi-sort-alpha-up"></i></a></th>
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
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1, true))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->urlName('admin/manage')
                ->render()
        );
    }

    public function testValue(): void
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
            <td data-label="name">test</td>
            </tr>
            <tr>
            <td data-label="id">1</td>
            <td data-label="name">test</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithValue())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testValueClosure(): void
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
                ->columns($this->createColumnsWithValueClosure())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10, 1))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    private function createColumns(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name'),
        ];
    }

    private function createColumnsWithContent(): array
    {
        return [
            DataColumn::create()
                ->attribute('id')
                ->content(
                    static fn (array|object $data, mixed $key, int $index): int => $data['id']
                ),
            DataColumn::create()
                ->attribute('name')
                ->content(
                    static fn (array|object $data, mixed $key, int $index): string => $data['name']
                ),
        ];
    }

    private function createColumnsWithContentAttributes(): array
    {
        return [
            DataColumn::create()
                ->attribute('id')
                ->content(
                    static fn (array|object $data, mixed $key, int $index): int => $data['id']
                )
                ->contentAttributes(['class' => 'test.class']),
            DataColumn::create()
                ->attribute('name')
                ->content(
                    static fn (array|object $data, mixed $key, int $index): string => $data['name']
                )
                ->contentAttributes(['class' => 'test.class']),
        ];
    }

    private function createColumnsWithContentAttributesClosure(): array
    {
        return [
            DataColumn::create()
                ->attribute('id')
                ->contentAttributes(
                    [
                        'class' => static fn (array|object $data, mixed $key, int $index): string => 'test.class',
                    ],
                ),
            DataColumn::create()
                ->attribute('name')
                ->contentAttributes(
                    [
                        'class' => static fn (array|object $data, mixed $key, int $index): string => 'test.class',
                    ],
                ),
        ];
    }

    private function createColumnsWithDataLabel(): array
    {
        return [
            DataColumn::create()->attribute('id')->dataLabel('test.id'),
            DataColumn::create()->attribute('name')->dataLabel('test.name'),
        ];
    }

    private function createColumnsWithLabel(): array
    {
        return [
            DataColumn::create()->attribute('id')->label('test.id'),
            DataColumn::create()->attribute('name')->label('test.username'),
        ];
    }

    private function createColumnsWithLabelMbString(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name')->label('Όνομα χρήστη'),
        ];
    }

    private function createColumnsWithLabelAttributes(): array
    {
        return [
            DataColumn::create()->attribute('id')->label('test.id')->labelAttributes(['class' => 'test.class']),
            DataColumn::create()->attribute('name')->label('test.username')->labelAttributes(['class' => 'test.class']),
        ];
    }

    private function createColumnsWithLinkSorter(): array
    {
        return [
            DataColumn::create()
                ->attribute('id')
                ->linkSorter('<a href="/admin/manage/1/5?sort=id" data-sort="id">id</a>'),
            DataColumn::create()
                ->attribute('name')
                ->linkSorter('<a href="/admin/manage/1/5?sort=name" data-sort="name">name</a>'),
        ];
    }

    private function createColumnsWithNotSorting(): array
    {
        return [
            DataColumn::create()->attribute('id')->withSorting(false),
            DataColumn::create()->attribute('name')->value('test'),
        ];
    }

    private function createColumnsWithName(): array
    {
        return [
            DataColumn::create()->attribute('id')->name('test.id'),
            DataColumn::create()->attribute('name')->name('test.username'),
        ];
    }

    private function createColumnsWithNotVisible(): array
    {
        return [
            DataColumn::create()->attribute('id'),
            DataColumn::create()->attribute('name')->visible(false),
        ];
    }

    private function createColumnsWithValue(): array
    {
        return [
            DataColumn::create()->attribute('id')->value(1),
            DataColumn::create()->attribute('name')->value('test'),
        ];
    }

    private function createColumnsWithValueClosure(): array
    {
        return [
            DataColumn::create()
                ->attribute('id')
                ->value(static fn (array|object $data): int => $data['id']),
            DataColumn::create()
                ->attribute('name')
                ->value(static fn (array|object $data): string => $data['name']),
        ];
    }
}
