<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Router\Route;
use Yiisoft\Yii\DataView\Column;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
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
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->afterRow(static fn () => '</div>')
                ->beforeRow(static fn () => '<div class="testMe">')
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testColumnGroupEnabled(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithAttributes())
                ->columnsGroupEnabled(true)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testColumnGroupEnabledEmpty(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->columnsGroupEnabled(true)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testColumnGuess(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <a name="view" href="/admin/view?id=1" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/update?id=1" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/delete?id=1" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            <tr>
            <td data-label="id">2</td>
            <td data-label="name">Mary</td>
            <td data-label="age">21</td>
            <td data-label="actions">
            <a name="view" href="/admin/view?id=2" title="View" role="button" style="text-decoration: none!important;"><span>🔎</span></a>
            <a name="update" href="/admin/update?id=2" title="Update" role="button" style="text-decoration: none!important;"><span>✎</span></a>
            <a name="delete" href="/admin/delete?id=2" title="Delete" role="button" style="text-decoration: none!important;"><span>❌</span></a>
            </td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns([])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
                ->urlName('admin')
                ->render()
        );
    }

    public function testEmptyCell(): void
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
            <td data-label="id">Empty cell</td>
            </tr>
            </tbody>
            </table>
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns([Column\DataColumn::create()->attribute('id')])
                ->emptyCell('Empty cell')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([['id' => '']], 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testEmptyText(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
                ->columns($this->createColumns())
                ->emptyText('Not found.')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([], 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testFooterRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumnsWithFooter())
                ->footerEnabled(true)
                ->footerRowAttributes(['class' => 'text-primary'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testHeader(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>List of users</div>
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->header('List of users')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testHeaderIntoGrid(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <div>List of users</div>
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->header('List of users')
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->layout('')
                ->layoutGridTable("{header}\n{items}\n{summary}")
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testHeaderRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->headerRowAttributes(['class' => 'text-primary'])
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testHeaderTableEnabledFalse(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->headerTableEnabled(false)
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testRenderEmptyData(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator([], 10))
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    public function testRowAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->rowAttributes(['class' => 'text-primary'])
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->rowAttributes(['class' => 'text-primary'])
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

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
            <div>gridview.summary</div>
            </div>
            HTML,
            GridView::widget()
                ->columns($this->createColumns())
                ->id('w1-grid')
                ->paginator($this->createOffsetPaginator($this->data, 10))
                ->tableAttributes(['class' => 'table table-striped table-bordered'])
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator([Route::get('/admin/manage')->name('admin/manage')]))
                ->render()
        );
    }

    private function createColumns(): array
    {
        return [
            Column\SerialColumn::create(),
            Column\DataColumn::create()->attribute('id'),
            Column\DataColumn::create()->attribute('name'),
            Column\DataColumn::create()->attribute('age'),
        ];
    }

    private function createColumnsWithAttributes(): array
    {
        return [
            Column\SerialColumn::create()->attributes(['class' => 'text-primary']),
            Column\DataColumn::create()->attribute('id')->attributes(['class' => 'bg-primary']),
            Column\DataColumn::create()->attribute('name')->attributes(['class' => 'bg-success']),
        ];
    }

    private function createColumnsWithFooter(): array
    {
        return [
            Column\SerialColumn::create()->footer('Total:'),
            Column\DataColumn::create()->attribute('id')->footer('2'),
            Column\DataColumn::create()->attribute('name')->footer('2'),
        ];
    }

    private function createColumnsWithTranslations(): array
    {
        return [
            Column\SerialColumn::create(),
            Column\DataColumn::create()->attribute('id'),
            Column\DataColumn::create()->attribute('name'),
            Column\ActionColumn::create(),
        ];
    }
}
