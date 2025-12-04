<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\KeysetPaginator;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Paginator\PageNotFoundException;
use Yiisoft\Data\Paginator\PageToken;
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
use Yiisoft\Yii\DataView\DataReaderNotSetException;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Factory\IncorrectValueException;
use Yiisoft\Yii\DataView\GridView\BodyRowContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Tests\Support\FakePaginator;
use Yiisoft\Yii\DataView\Tests\Support\FilterableSortableLimitableDataReader;
use Yiisoft\Yii\DataView\Tests\Support\SimplePaginationUrlCreator;
use Yiisoft\Yii\DataView\Tests\Support\SimpleReadable;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;
use Yiisoft\Yii\DataView\Tests\Support\StringEnum;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

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
            ]),
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
                    filterValidation: new Integer(max: 120),
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
                    filterValidation: new Integer(max: 120),
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
                },
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
                    filterFactory: new class implements FilterFactoryInterface {
                        public function create(string $property, string $value): FilterInterface
                        {
                            throw new IncorrectValueException();
                        }
                    },
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

    public function testPageParameterName(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1)->withCurrentPage(1);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageParameterName('p')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <nav>
            <a href="/route?">⟪</a>
            <a href="/route?">⟨</a>
            <a href="/route?">1</a>
            <a href="/route?p=2">2</a>
            <a href="/route?p=2">⟩</a>
            <a href="/route?p=2">⟫</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testPreviousPageParameterName(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3]];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1)->withToken(PageToken::next('1'));

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->previousPageParameterName('pp')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <nav>
            <a href="/route?pp=2">⟨</a>
            <a href="/route?page=2">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testPageSizeParameterName(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(2);

        $html = $this->createGridView($paginator)
            ->filterFormId('FID')
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageSizeParameterName('ps')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['ps' => '5']))
            ->pageSizeConstraint(false)
            ->columns(new DataColumn('id', filter: true))
            ->render();

        $this->assertStringContainsString('<input type="hidden" name="ps" value="5">', $html);
        $this->assertStringContainsString('<input type="text" value="5" data-default-page-size="2"', $html);
    }

    public function testSortParameterName(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id']));

        $html = $this->createGridView($dataReader)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->sortParameterName('s')
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString('<th><a href="/route?s=id">Id</a></th>', $html);
    }

    public function testPageParameterType(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1)->withCurrentPage(1);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageParameterType(UrlParameterType::Path)
            ->render();

        $this->assertStringContainsString('<a href="/route/page-2?">2</a>', $html);
    }

    public function testPreviousPageParameterType(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3]];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(1)->withToken(PageToken::next('1'));

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->previousPageParameterType(UrlParameterType::Path)
            ->render();

        $this->assertStringContainsString('<a href="/route/prev-page-2?">⟨</a>', $html);
    }

    public function testPageSizeParameterType(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(2);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageSizeParameterType(UrlParameterType::Path)
            ->urlParameterProvider(new SimpleUrlParameterProvider())
            ->pageSizeConstraint(false)
            ->render();

        $this->assertStringContainsString('data-url-pattern="/route/pagesize-YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER?', $html);
    }

    public function testSortParameterType(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id']));

        $html = $this->createGridView($dataReader)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->sortParameterType(UrlParameterType::Path)
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString('<th><a href="/route/sort-id?">Id</a></th>', $html);
    }

    public function testUrlArguments(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1)->withCurrentPage(1);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->urlArguments(['category' => 'books', 'author' => 'smith'])
            ->render();

        $this->assertStringContainsString('<a href="/route/category-books/author-smith?page=2">2</a>', $html);
    }

    public function testUrlQueryParameters(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1)->withCurrentPage(1);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->urlQueryParameters(['filter' => 'active', 'lang' => 'en'])
            ->render();

        $this->assertStringContainsString('<a href="/route?filter=active&amp;lang=en&amp;page=2">2</a>', $html);
    }

    public function testMultiSort(): void
    {
        $data = [
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 1, 'name' => 'Charlie'],
            ['id' => 1, 'name' => 'Anna'],
        ];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any(['id', 'name']));

        $html = $this->createGridView($dataReader)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->urlParameterProvider(new SimpleUrlParameterProvider(['sort' => 'id,name']))
            ->multiSort()
            ->columns(
                new DataColumn('id'),
                new DataColumn('name'),
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th><a href="/route?sort=-id%2Cname">Id</a></th>
            <th><a href="/route?sort=id%2C-name">Name</a></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>1</td>
            <td>Anna</td>
            </tr>
            <tr>
            <td>1</td>
            <td>Charlie</td>
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

    public function testContainerTagEmptyString(): void
    {
        $gridView = $this->createGridView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $gridView->containerTag('');
    }

    public function testIgnoreMissingPage(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);

        $html = $this->createGridView($paginator)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['page' => '999']))
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString('<div>Page <b>1</b> of <b>2</b></div>', $html);
    }

    public function testIgnoreMissingPage2(): void
    {
        $dataReader = new IterableDataReader([]);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1)->withCurrentPage(999);

        $widget = $this->createGridView($paginator);

        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('Page 999 not found.');
        $widget->render();
    }

    public function testNotIgnoreMissingPage(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);

        $widget = $this->createGridView($paginator)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['page' => '999']))
            ->ignoreMissingPage(false)
            ->columns(new DataColumn('id'));

        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('Page 999 not found.');
        $widget->render();
    }

    public function testPageNotFoundExceptionCallback(): void
    {
        $data = [['id' => 1], ['id' => 2]];
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader))->withPageSize(1);

        $capturedException = null;

        $widget = $this->createGridView($paginator)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['page' => '999']))
            ->ignoreMissingPage(false)
            ->pageNotFoundExceptionCallback(
                function ($exception) use (&$capturedException) {
                    $capturedException = $exception;
                },
            )
            ->columns(new DataColumn('id'));

        try {
            $widget->render();
        } catch (PageNotFoundException $thrownException) {
        }

        $this->assertSame($thrownException, $capturedException);
    }

    public function testContainerAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->containerAttributes(['class' => 'red', 'data-key' => 'test'])
            ->containerAttributes(['class' => 'custom-grid', 'data-test' => 'grid'])
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div class="custom-grid" data-test="grid">
            <table>
            HTML,
            $html,
        );
    }

    public function testId(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->id('my-grid')
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div id="my-grid">
            <table>
            HTML,
            $html,
        );
    }

    public function testContainerClass(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->containerClass('red')
            ->containerClass('blue', StringEnum::GREEN)
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div class="blue green">
            <table>
            HTML,
            $html,
        );
    }

    public function testAddContainerClass(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->containerClass('red')
            ->addContainerClass('blue', StringEnum::GREEN)
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div class="red blue green">
            <table>
            HTML,
            $html,
        );
    }

    public function testPrepend(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->prepend('<h1>Grid Title</h1>', '<p>Description</p>')
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <h1>Grid Title</h1><p>Description</p>
            <table>
            HTML,
            $html,
        );
    }

    public function testAppend(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->append('<div>Footer</div>', '<p>Copyright</p>')
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            <div>Footer</div><p>Copyright</p>
            </div>
            HTML,
            $html,
        );
    }

    public function testHeader(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <div>Grid Header</div>

            <table>
            HTML,
            $html,
        );
    }

    public function testHeaderTag(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->headerTag('h2')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <h2>Grid Header</h2>

            <table>
            HTML,
            $html,
        );
    }

    public function testWithoutHeaderTag(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->headerTag(null)
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            Grid Header

            <table>
            HTML,
            $html,
        );
    }

    public function testHeaderTagEmptyString(): void
    {
        $gridView = $this->createGridView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $gridView->headerTag('');
    }

    public function testHeaderAttributes(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->headerAttributes(['class' => 'grid-header', 'data-test' => 'header'])
            ->render();

        $this->assertStringContainsString('<div class="grid-header" data-test="header">Grid Header</div>', $html);
    }

    public function testHeaderClass(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->headerClass('header-primary', StringEnum::GREEN)
            ->render();

        $this->assertStringContainsString('<div class="header-primary green">Grid Header</div>', $html);
    }

    public function testAddHeaderClass(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('Grid Header')
            ->headerClass('header-primary')
            ->addHeaderClass('text-center', StringEnum::GREEN)
            ->render();

        $this->assertStringContainsString('<div class="header-primary text-center green">Grid Header</div>', $html);
    }

    #[TestWith(['> BOLD', false])]
    #[TestWith(['&gt; BOLD', true])]
    public function testEncodeHeader(string $expected, bool $encode): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('> BOLD')
            ->encodeHeader($encode)
            ->render();

        $this->assertStringContainsString('<div>' . $expected . '</div>', $html);
    }

    public function testToolbar(): void
    {
        $html = $this->createGridView([['id' => 1]])
            ->header('HEADER')
            ->toolbar('<div class="toolbar">Toolbar Content</div>')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <div>HEADER</div>
            <div class="toolbar">Toolbar Content</div>
            <table>
            HTML,
            $html,
        );
    }

    public function testSummaryTag(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([['id' => 1], ['id' => 2], ['id' => 3]]));
        $html = $this->createGridView($paginator)
            ->summaryTag('p')
            ->render();

        $this->assertStringContainsString('<p>Page <b>1</b> of <b>1</b></p>', $html);
    }

    public function testSummaryTagNull(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([['id' => 1], ['id' => 2]]));
        $html = $this->createGridView($paginator)
            ->summaryTag(null)
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            </table>
            Page <b>1</b> of <b>1</b>
            HTML,
            $html,
        );
    }

    public function testSummaryTagEmptyString(): void
    {
        $gridView = $this->createGridView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $gridView->summaryTag('');
    }

    public function testSummaryAttributes(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([['id' => 1], ['id' => 2]]));
        $html = $this->createGridView($paginator)
            ->summaryAttributes(['class' => 'summary-info', 'data-test' => 'summary'])
            ->render();

        $this->assertStringContainsString(
            '<div class="summary-info" data-test="summary">Page <b>1</b> of <b>1</b></div>',
            $html,
        );
    }

    public function testSummaryTemplate(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader([['id' => 1], ['id' => 2], ['id' => 3]]));
        $html = $this->createGridView($paginator)
            ->summaryTemplate('Showing {begin}-{end} of {totalCount} items')
            ->render();

        $this->assertStringContainsString('<div>Showing 1-3 of 3 items</div>', $html);
    }

    public function testPaginationWidget(): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $paginator = (new OffsetPaginator(new IterableDataReader($data)))->withPageSize(5);
        $paginationWidget = (new OffsetPagination())
            ->labelPrevious('Previous PAGE')
            ->labelNext('Next PAGE');

        $html = $this->createGridView($paginator)
            ->paginationWidget($paginationWidget)
            ->render();

        $this->assertStringContainsString('Previous PAGE', $html);
        $this->assertStringContainsString('Next PAGE', $html);
    }

    public function testOffsetPaginationConfig(): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $paginator = (new OffsetPaginator(new IterableDataReader($data)))->withPageSize(5);

        $html = $this->createGridView($paginator)
            ->offsetPaginationConfig([
                'labelPrevious()' => ['Custom Previous'],
                'labelNext()' => ['Custom Next'],
            ])
            ->render();

        $this->assertStringContainsString('Custom Previous', $html);
        $this->assertStringContainsString('Custom Next', $html);
    }

    public function testKeysetPaginationConfig(): void
    {
        $data = [];
        for ($i = 1; $i <= 20; $i++) {
            $data[] = ['id' => $i];
        }
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any()->withOrder(['id' => 'asc']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(5);

        $html = $this->createGridView($paginator)
            ->keysetPaginationConfig([
                'labelPrevious()' => ['Custom Prev'],
                'labelNext()' => ['Custom Nxt'],
            ])
            ->urlCreator(new SimplePaginationUrlCreator())
            ->render();

        $this->assertStringContainsString('Custom Prev', $html);
        $this->assertStringContainsString('Custom Nxt', $html);
    }

    public function testPageSizeWidget(): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $paginator = (new OffsetPaginator(new IterableDataReader($data)));
        $pageSizeWidget = (new SelectPageSize())->addAttributes(['class' => 'custom-select']);

        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->pageSizeWidget($pageSizeWidget)
            ->render();

        $this->assertStringContainsString('class="custom-select"', $html);
    }

    public function testPageSizeTag(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader(array_fill(0, 20, ['id' => 1])));
        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->pageSizeTag('section')
            ->render();

        $this->assertStringContainsString('<section>Results per page', $html);
    }

    public function testWithoutPageSizeTag(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader(array_fill(0, 20, ['id' => 1])));
        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->pageSizeTag(null)
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            </nav>
            Results per page
            HTML,
            $html,
        );
    }

    public function testPageSizeTagEmptyString(): void
    {
        $gridView = $this->createGridView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $gridView->pageSizeTag('');
    }

    public function testPageSizeAttributes(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader(array_fill(0, 20, ['id' => 1])));
        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->pageSizeAttributes(['class' => 'page-size-wrapper', 'data-test' => 'pagesize'])
            ->render();

        $this->assertStringContainsString(
            '<div class="page-size-wrapper" data-test="pagesize">Results per page',
            $html,
        );
    }

    public function testPageSizeTemplate(): void
    {
        $paginator = new OffsetPaginator(new IterableDataReader(array_fill(0, 20, ['id' => 1])));
        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->pageSizeTemplate('Items: {widget}')
            ->render();

        $this->assertStringContainsString('<div>Items: <select', $html);
    }

    public function testRenderWithoutDataReader(): void
    {
        $gridView = new GridView(new Container());

        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        $gridView->render();
    }

    #[TestWith([4, 5])]
    #[TestWith([2, 20])]
    public function testPageSizeConstraintWithInt(int $expectedCountPages, int $constraint): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $dataReader = new IterableDataReader($data);
        $paginator = (new OffsetPaginator($dataReader));
        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageSizeConstraint($constraint)
            ->render();

        $this->assertStringContainsString('Page <b>1</b> of <b>' . $expectedCountPages . '</b>', $html);
    }

    public function testAutoCreateKeysetPaginator(): void
    {
        $data = [];
        for ($i = 1; $i <= 20; $i++) {
            $data[] = ['id' => $i];
        }

        $sort = Sort::any()->withOrder(['id' => 'asc']);
        $dataReader = (new FilterableSortableLimitableDataReader($data))->withSort($sort);

        $html = $this->createGridView($dataReader)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <nav>
            <a>⟨</a>
            <a href="/route?page=10">⟩</a>
            </nav>
            HTML,
            $html,
        );
    }

    public function testWithoutPaginator(): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $dataReader = new FilterableSortableLimitableDataReader($data);

        $html = $this->createGridView($dataReader)
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            </table>
            </div>
            HTML,
            $html,
        );
    }

    public function testPreviousPageParameter(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5]];
        $dataReader = (new IterableDataReader($data))->withSort(Sort::any()->withOrder(['id' => 'asc']));
        $paginator = (new KeysetPaginator($dataReader))->withPageSize(2);

        $html = $this->createGridView($paginator)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['prev-page' => '5']))
            ->urlCreator(new SimplePaginationUrlCreator())
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>3</td>
            </tr>
            <tr>
            <td>4</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testInvalidPageSizeWithoutPageSizeConstraint(): void
    {
        $data = array_fill(0, 20, ['id' => 1]);
        $paginator = new OffsetPaginator(new IterableDataReader($data));

        $html = $this->createGridView($paginator)
            ->pageSizeConstraint(false)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['pagesize' => '0']))
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString('Page <b>1</b> of <b>2</b>', $html);
    }

    public function testPageSizeWithIntConstraint(): void
    {
        $data = array_fill(0, 50, ['id' => 1]);
        $paginator = new OffsetPaginator(new IterableDataReader($data));

        $html = $this->createGridView($paginator)
            ->pageSizeConstraint(20)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['pagesize' => '5']))
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringContainsString('Page <b>1</b> of <b>10</b>', $html);
    }

    public function testPageSizeWithArrayConstraint(): void
    {
        $data = array_fill(0, 50, ['id' => 1]);
        $paginator = new OffsetPaginator(new IterableDataReader($data));

        $html = $this->createGridView($paginator)
            ->pageSizeConstraint([5, 10, 20])
            ->urlParameterProvider(new SimpleUrlParameterProvider(['pagesize' => '20']))
            ->columns(new DataColumn('id'))
            ->render();

        // Should have 5 pages (50 items / 10 per page)
        $this->assertStringContainsString('Page <b>1</b> of <b>3</b>', $html);
    }

    public function testPageSizeExceedsIntConstraint(): void
    {
        $data = array_fill(0, 50, ['id' => 1]);
        $paginator = new OffsetPaginator(new IterableDataReader($data));

        $html = $this->createGridView($paginator)
            ->pageSizeConstraint(20)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['pagesize' => '30']))
            ->columns(new DataColumn('id'))
            ->render();

        // Should use default page size (10), resulting in 5 pages
        $this->assertStringContainsString('Page <b>1</b> of <b>5</b>', $html);
    }

    public function testUnsupportedPaginator(): void
    {
        $paginator = new FakePaginator([['id' => 1], ['id' => 2]]);

        $html = $this->createGridView($paginator)
            ->urlCreator(new SimplePaginationUrlCreator())
            ->pageSizeConstraint(10)
            ->columns(new DataColumn('id'))
            ->render();

        $this->assertStringStartsWith(
            <<<HTML
            <div>
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


            <div>Results per page
            HTML,
            $html,
        );
    }

    public function testImmutability(): void
    {
        $gridView = $this->createGridView();

        $this->assertNotSame($gridView, $gridView->addColumnRendererConfigs([]));
        $this->assertNotSame($gridView, $gridView->filterCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->filterCellInvalidClass('invalid'));
        $this->assertNotSame($gridView, $gridView->filterErrorsContainerAttributes([]));
        $this->assertNotSame($gridView, $gridView->filterFormId('form'));
        $this->assertNotSame($gridView, $gridView->filterFormAttributes([]));
        $this->assertNotSame($gridView, $gridView->keepPageOnSort());
        $this->assertNotSame($gridView, $gridView->afterRow(null));
        $this->assertNotSame($gridView, $gridView->beforeRow(null));
        $this->assertNotSame($gridView, $gridView->columns());
        $this->assertNotSame($gridView, $gridView->columnGrouping());
        $this->assertNotSame($gridView, $gridView->emptyCell('test'));
        $this->assertNotSame($gridView, $gridView->emptyCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->enableFooter());
        $this->assertNotSame($gridView, $gridView->footerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->enableHeader());
        $this->assertNotSame($gridView, $gridView->headerRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->bodyRowAttributes([]));
        $this->assertNotSame($gridView, $gridView->tableAttributes([]));
        $this->assertNotSame($gridView, $gridView->addTableClass('test'));
        $this->assertNotSame($gridView, $gridView->tableClass('test'));
        $this->assertNotSame($gridView, $gridView->tbodyAttributes([]));
        $this->assertNotSame($gridView, $gridView->addTbodyClass('test'));
        $this->assertNotSame($gridView, $gridView->tbodyClass('test'));
        $this->assertNotSame($gridView, $gridView->headerCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->bodyCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->sortableLinkAttributes([]));
        $this->assertNotSame($gridView, $gridView->sortableHeaderPrepend('test'));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAppend('test'));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAscPrepend('test'));
        $this->assertNotSame($gridView, $gridView->sortableHeaderAscAppend('test'));
        $this->assertNotSame($gridView, $gridView->sortableHeaderDescPrepend('test'));
        $this->assertNotSame($gridView, $gridView->sortableHeaderDescAppend('test'));
        $this->assertNotSame($gridView, $gridView->noResultsCellAttributes([]));
        $this->assertNotSame($gridView, $gridView->pageParameterName('p'));
        $this->assertNotSame($gridView, $gridView->previousPageParameterName('pp'));
        $this->assertNotSame($gridView, $gridView->pageSizeParameterName('ps'));
        $this->assertNotSame($gridView, $gridView->sortParameterName('s'));
        $this->assertNotSame($gridView, $gridView->pageParameterType(UrlParameterType::Path));
        $this->assertNotSame($gridView, $gridView->previousPageParameterType(UrlParameterType::Path));
        $this->assertNotSame($gridView, $gridView->pageSizeParameterType(UrlParameterType::Path));
        $this->assertNotSame($gridView, $gridView->sortParameterType(UrlParameterType::Path));
        $this->assertNotSame($gridView, $gridView->urlArguments(['id' => 1]));
        $this->assertNotSame($gridView, $gridView->urlQueryParameters(['filter' => 'active']));
        $this->assertNotSame($gridView, $gridView->multiSort());
        $this->assertNotSame($gridView, $gridView->ignoreMissingPage(false));
        $this->assertNotSame($gridView, $gridView->pageNotFoundExceptionCallback(fn() => null));
        $this->assertNotSame($gridView, $gridView->containerAttributes([]));
        $this->assertNotSame($gridView, $gridView->id('test'));
        $this->assertNotSame($gridView, $gridView->containerClass('test'));
        $this->assertNotSame($gridView, $gridView->addContainerClass('test'));
        $this->assertNotSame($gridView, $gridView->prepend('test'));
        $this->assertNotSame($gridView, $gridView->append('test'));
        $this->assertNotSame($gridView, $gridView->header('test'));
        $this->assertNotSame($gridView, $gridView->headerTag('h1'));
        $this->assertNotSame($gridView, $gridView->headerAttributes([]));
        $this->assertNotSame($gridView, $gridView->headerClass('test'));
        $this->assertNotSame($gridView, $gridView->addHeaderClass('test'));
        $this->assertNotSame($gridView, $gridView->encodeHeader(false));
        $this->assertNotSame($gridView, $gridView->toolbar('test'));
        $this->assertNotSame($gridView, $gridView->summaryTag('p'));
        $this->assertNotSame($gridView, $gridView->summaryAttributes([]));
        $this->assertNotSame($gridView, $gridView->summaryTemplate('test'));
        $this->assertNotSame($gridView, $gridView->paginationWidget(null));
        $this->assertNotSame($gridView, $gridView->offsetPaginationConfig([]));
        $this->assertNotSame($gridView, $gridView->keysetPaginationConfig([]));
        $this->assertNotSame($gridView, $gridView->pageSizeWidget(null));
        $this->assertNotSame($gridView, $gridView->pageSizeTag('p'));
        $this->assertNotSame($gridView, $gridView->pageSizeAttributes([]));
        $this->assertNotSame($gridView, $gridView->pageSizeTemplate('test'));
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
