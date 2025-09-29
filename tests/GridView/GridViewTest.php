<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Td;
use Yiisoft\Html\Tag\Tr;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Factory\IncorrectValueException;
use Yiisoft\Yii\DataView\GridView\BodyRowContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\SimplePaginationUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\SimpleReadable;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;

final class GridViewTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Eva'],
        ])
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
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
            <td>Anna</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Eva</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            $html,
        );
    }

    public function testReadableOnly(): void
    {
        $html = $this->createGridView(
            new SimpleReadable([
                ['id' => 1, 'name' => 'Anna'],
                ['id' => 2, 'name' => 'Eva'],
            ])
        )
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
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
            <td>Anna</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Eva</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html,
        );
    }

    public function testFilterCellAttributes(): void
    {
        $html = $this->createGridView()
            ->filterFormId('FID')
            ->filterCellAttributes(['class' => 'filter-cell', 'data-test' => 'filter'])
            ->columns(
                new DataColumn(property: 'name', filter: true),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tr>
            <td class="filter-cell" data-test="filter"><input type="text" name="name" form="FID"></td>
            </tr>
            HTML,
            $html,
        );
    }

    public function testFilterCellInvalidClass(): void
    {
        $html = $this->createGridView()
            ->filterFormId('FID')
            ->filterCellInvalidClass('invalid-filter')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['age' => '150']))
            ->columns(
                new DataColumn(
                    property: 'age',
                    filter: true,
                    filterValidation: new Integer(max: 120)
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td class="invalid-filter"><input type="text" name="age" value="150" form="FID"><div><div>Value must be no greater than 120.</div></div></td>
            HTML,
            $html,
        );
    }

    public function testFilterErrorsContainerAttributes(): void
    {
        $html = $this->createGridView()
            ->filterFormId('FID')
            ->filterErrorsContainerAttributes(['class' => 'errors-container'])
            ->urlParameterProvider(new SimpleUrlParameterProvider(['age' => '150']))
            ->columns(
                new DataColumn(
                    property: 'age',
                    filter: true,
                    filterValidation: new Integer(max: 120)
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="text" name="age" value="150" form="FID"><div class="errors-container"><div>Value must be no greater than 120.</div></div></td>
            HTML,
            $html,
        );
    }

    public function testFilterForm(): void
    {
        $html = $this->createGridView()
            ->filterFormId('FID')
            ->columns(
                new DataColumn(
                    property: 'age',
                    filter: true,
                ),
            )
            ->render();

        $this->assertStringContainsString(
            '<form id="FID" action method="GET" style="display:none"><button type="submit">Submit</button></form><table>',
            $html,
        );
        $this->assertStringContainsString(
            '<td><input type="text" name="age" form="FID"></td>',
            $html,
        );
    }

    public function testFilterFormAttributes(): void
    {
        $html = $this->createGridView()
            ->filterFormId('FID')
            ->filterFormAttributes(['class' => 'filter-form', 'data-form' => 'grid-filter'])
            ->columns(
                new DataColumn(
                    property: 'name',
                    filter: true,
                ),
            )
            ->render();

        $this->assertStringContainsString(
            '<form id="FID" class="filter-form" action method="GET" data-form="grid-filter" style="display:none">',
            $html,
        );
    }

    #[TestWith(['/route?page=2&amp;sort=id', true])]
    #[TestWith(['/route?sort=id', false])]
    public function testKeepPageOnSort(string $expectedUrl, bool $keepPageOnSort): void
    {
        $data = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];
        $dataReader = (new IterableDataReader($data))
            ->withSort(Sort::any(['id', 'name']));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(2)
            ->withCurrentPage(2);

        $html = $this->createGridView()
            ->dataReader($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->keepPageOnSort($keepPageOnSort)
            ->columns(
                new DataColumn(property: 'id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<th><a href="' . $expectedUrl . '">Id</a></th>',
            $html,
        );
    }

    public function testAfterRow(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->afterRow(
                static function (array $data, mixed $key, int $index, GridView $widget): Tr {
                    return Html::tr(['class' => 'after-row'])
                        ->cells(Td::tag()->content('After row ' . $data['id'])->colSpan(2));
                }
            )
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>1</td>
            <td>Anna</td>
            </tr>
            <tr class="after-row">
            <td colspan="2">After row 1</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Bob</td>
            </tr>
            <tr class="after-row">
            <td colspan="2">After row 2</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testBeforeRow(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->beforeRow(function (array $data, mixed $key, int $index, GridView $widget): Tr {
                return Html::tr(['class' => 'before-row'])
                    ->cells(Td::tag()->content('Before row ' . $data['id'])->attributes(['colspan' => '2']));
            })
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr class="before-row">
            <td colspan="2">Before row 1</td>
            </tr>
            <tr>
            <td>1</td>
            <td>Anna</td>
            </tr>
            <tr class="before-row">
            <td colspan="2">Before row 2</td>
            </tr>
            <tr>
            <td>2</td>
            <td>Bob</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testEmptyCell(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => null],
        ])
            ->emptyCell('--')
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>1</td>
            <td>--</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testEmptyCellWithAttributes(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => null],
        ])
            ->emptyCell('N/A', ['class' => 'empty-cell'])
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>1</td>
            <td class="empty-cell">N/A</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testEmptyCellAttributes(): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => null],
        ])
            ->emptyCell('Empty')
            ->emptyCellAttributes(['class' => 'no-content'])
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            '<td class="no-content">Empty</td>',
            $html,
        );
    }

    public function testFooterRowAttributes(): void
    {
        $html = $this->createGridView()
            ->footerRowAttributes(['class' => 'footer-row'])
            ->enableFooter()
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tfoot>
            <tr class="footer-row">
            <td>&nbsp;</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public function testDisableHeader(): void
    {
        $html = $this->createGridView()
            ->enableHeader(false)
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <table>
            <tbody>
            <tr>
            <td colspan="1">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html,
        );
    }

    public function testHeaderRowAttributes(): void
    {
        $html = $this->createGridView()
            ->headerRowAttributes(['class' => 'header-row', 'data-header' => 'grid'])
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr class="header-row" data-header="grid">
            <th>Id</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public static function dataBodyRowAttributes(): iterable
    {
        yield 'array' => [
            '<tr class="body-row" data-row="grid">',
            ['class' => 'body-row', 'data-row' => 'grid'],
        ];
        yield 'closure' => [
            '<tr class="row-1" data-id="1">',
            static fn(array $data, BodyRowContext $context) => ['class' => 'row-' . $data['id'], 'data-id' => $context->data['id']],
        ];
        yield 'nested-closure' => [
            '<tr class="row-1" data-row="grid">',
            [
                'class' => static fn(array $data, BodyRowContext $context) => 'row-' . $data['id'],
                'data-row' => 'grid',
            ],
        ];
    }

    #[DataProvider('dataBodyRowAttributes')]
    public function testBodyRowAttributes(string $expectedTr, mixed $attributes): void
    {
        $html = $this->createGridView([
            ['id' => 1, 'name' => 'Anna'],
        ])
            ->bodyRowAttributes($attributes)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            $expectedTr
            <td>1</td>
            <td>Anna</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testTableAttributes(): void
    {
        $html = $this->createGridView()
            ->tableAttributes(['class' => 'custom-table', 'id' => 'my-grid'])
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<table id="my-grid" class="custom-table">',
            $html,
        );
    }

    public function testAddTableClass(): void
    {
        $html = $this->createGridView()
            ->tableAttributes(['class' => 'existing-class'])
            ->addTableClass('new-class', 'another-class')
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<table class="existing-class new-class another-class">',
            $html,
        );
    }

    public function testTableClass(): void
    {
        $html = $this->createGridView()
            ->tableAttributes(['class' => 'existing-class'])
            ->tableClass('new-class', 'another-class')
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<table class="new-class another-class">',
            $html,
        );
    }

    public function testTbodyAttributes(): void
    {
        $html = $this->createGridView()
            ->tbodyAttributes(['class' => 'custom-tbody', 'data-tbody' => 'grid'])
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<tbody class="custom-tbody" data-tbody="grid">',
            $html,
        );
    }

    public function testAddTbodyClass(): void
    {
        $html = $this->createGridView()
            ->tbodyAttributes(['class' => 'existing-class'])
            ->addTbodyClass('new-class', 'another-class')
            ->columns(
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            '<tbody class="existing-class new-class another-class">',
            $html,
        );
    }

    public function testTbodyClass(): void
    {
        $html = $this->createGridView()
            ->tbodyAttributes(['class' => 'existing-class'])
            ->tbodyClass('new-class', 'another-class')
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString(
            '<tbody class="new-class another-class">',
            $html,
        );
    }

    public function testHeaderCellAttributes(): void
    {
        $html = $this->createGridView()
            ->headerCellAttributes(['class' => 'header-cell', 'data-sort' => 'enabled'])
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th class="header-cell" data-sort="enabled">Id</th>
            <th class="header-cell" data-sort="enabled">Name</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public static function dataBodyCellAttributes(): iterable
    {
        yield 'array' => [
            '<td class="body-cell" data-cell="data">',
            ['class' => 'body-cell', 'data-cell' => 'data'],
        ];
        yield 'nested-callable' => [
            '<td class="body-cell" data-body="Anna">',
            [
                'class' => 'body-cell',
                'data-body' => static fn(array $data, DataContext $context) => $data['name'],
            ],
        ];
    }

    #[DataProvider('dataBodyCellAttributes')]
    public function testBodyCellAttributes(string $expectedTd, mixed $attributes): void
    {
        $html = $this->createGridView([
            ['name' => 'Anna'],
        ])
            ->bodyCellAttributes($attributes)
            ->columns(
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            {$expectedTd}Anna</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testSortableLinkAttributes(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]))->withSort(Sort::any(['id', 'name']));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->sortableLinkAttributes(['class' => 'sort-link', 'data-sort' => 'enabled'])
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th><a class="sort-link" href="/route?sort=id" data-sort="enabled">Id</a></th>
            <th><a class="sort-link" href="/route?sort=name" data-sort="enabled">Name</a></th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderPrepend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
        ]))->withSort(Sort::any(['id', 'name']));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderPrepend('↕ ')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>↕ <a href="#">Id</a></th>
            <th>↕ <a href="#">Name</a></th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderAppend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
        ]))->withSort(Sort::any(['id', 'name']));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderAppend(' ⟷')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th><a href="#">Id</a> ⟷</th>
            <th><a href="#">Name</a> ⟷</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderAscPrepend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]))->withSort(Sort::any(['id', 'name'])->withOrderString('id'));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderPrepend('↕ ')
            ->sortableHeaderAppend(' ⟷')
            ->sortableHeaderAscPrepend('↑ ')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>↑ <a href="#">Id</a></th>
            <th>↕ <a href="#">Name</a> ⟷</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderAscAppend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]))->withSort(Sort::any(['id', 'name'])->withOrderString('id'));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderPrepend('↕ ')
            ->sortableHeaderAppend(' ⟷')
            ->sortableHeaderAscAppend(' ↑')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th><a href="#">Id</a> ↑</th>
            <th>↕ <a href="#">Name</a> ⟷</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderDescPrepend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]))->withSort(Sort::any(['id', 'name'])->withOrderString('-id'));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderPrepend('↕ ')
            ->sortableHeaderAppend(' ⟷')
            ->sortableHeaderDescPrepend('↓ ')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>↓ <a href="#">Id</a></th>
            <th>↕ <a href="#">Name</a> ⟷</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testSortableHeaderDescAppend(): void
    {
        $dataReader = (new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]))->withSort(Sort::any(['id', 'name'])->withOrderString('-id'));

        $html = $this->createGridView()
            ->dataReader($dataReader)
            ->sortableHeaderPrepend('↕ ')
            ->sortableHeaderAppend(' ⟷')
            ->sortableHeaderDescAppend(' ↓')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th><a href="#">Id</a> ↓</th>
            <th>↕ <a href="#">Name</a> ⟷</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testNoResultsCellAttributes(): void
    {
        $html = $this->createGridView()
            ->noResultsCellAttributes(['class' => 'no-results', 'data-empty' => 'true'])
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td class="no-results" colspan="2" data-empty="true">No results found.</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testNoResultsText(): void
    {
        $html = $this->createGridView()
            ->noResultsText('No data available')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td colspan="2">No data available</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testNoResultsTemplate(): void
    {
        $html = $this->createGridView()
            ->noResultsText('No records')
            ->noResultsTemplate('<em>{text}</em>')
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td colspan="2"><em>No records</em></td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testIncorrectValueInFilter(): void
    {
        $html = $this->createGridView([['name' => 'Hello world']])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['name' => 'Hello']))
            ->columns(
                new DataColumn(
                    'name',
                    filter: true,
                    filterFactory: new class () implements FilterFactoryInterface {
                        public function create(string $property, string $value): FilterInterface
                        {
                            throw new IncorrectValueException();
                        }
                    }
                ),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <form id="FID" action method="GET" style="display:none"><button type="submit">Submit</button></form><table>
            <thead>
            <tr>
            <th>Name</th>
            </tr>
            <tr>
            <td><input type="text" name="name" value="Hello" form="FID"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="1">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            $html,
        );
    }

    public function testFilterFormWithPageSize(): void
    {
        $html = $this->createGridView([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ])
            ->filterFormId('FID')
            ->pageSizeConstraint(false)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['pagesize' => '2']))
            ->columns(
                new DataColumn('id', filter: true),
            )
            ->render();

        $this->assertStringContainsString(
            '<form id="FID" action method="GET" style="display:none"><button type="submit">Submit</button><input type="hidden" name="pagesize" value="2"></form>',
            $html,
        );
    }

    public function testFilterFormWithSort(): void
    {
        $html = $this->createGridView([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ])
            ->filterFormId('FID')
            ->pageSizeConstraint(false)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['sort' => 'id']))
            ->columns(
                new DataColumn('id', filter: true),
            )
            ->render();

        $this->assertStringContainsString(
            '<form id="FID" action method="GET" style="display:none"><button type="submit">Submit</button><input type="hidden" name="sort" value="id"></form>',
            $html,
        );
    }

    public static function dataWithSortFromUrl(): iterable
    {
        yield [
            <<<HTML
            <tbody>
            <tr>
            <td>3</td>
            </tr>
            <tr>
            <td>2</td>
            </tr>
            <tr>
            <td>1</td>
            </tr>
            </tbody>
            HTML,
            Sort::only(['id']),
        ];
        yield [
            <<<HTML
            <tbody>
            <tr>
            <td>1</td>
            </tr>
            <tr>
            <td>2</td>
            </tr>
            <tr>
            <td>3</td>
            </tr>
            </tbody>
            HTML,
            Sort::only(['name']),
        ];
    }

    #[DataProvider('dataWithSortFromUrl')]
    public function testWithSortFromUrl(string $expectedTbody, Sort $sort): void
    {
        $data = [
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Charlie'],
        ];
        $dataReader = (new IterableDataReader($data))->withSort($sort);
        $html = $this->createGridView($dataReader)
            ->filterFormId('FID')
            ->pageSizeConstraint(false)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['sort' => '-id']))
            ->columns(
                new DataColumn('id'),
            )
            ->render();

        $this->assertStringContainsString($expectedTbody, $html);
    }

    private function createGridView(ReadableDataInterface|array $data = []): GridView
    {
        $container = new Container(
            ContainerConfig::create()
                ->withDefinitions([
                    ValidatorInterface::class => Validator::class,
                ]),
        );

        $dataReader = $data instanceof ReadableDataInterface ? $data : new IterableDataReader($data);

        return (new GridView($container))->dataReader($dataReader);
    }
}
