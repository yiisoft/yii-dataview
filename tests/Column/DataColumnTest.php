<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\NoEncode;
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
            <th>Name</th>
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

    public function testSort(): void
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

    public function testColumnClasses(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <table>
            <colgroup>
            <col class="columnClassAttr columnClass" custom="columnAttributes">
            </colgroup>
            <thead>
            <tr>
            <th class="headerClassAttr headerClass" custom="headerAttributes">Name</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td class="bodyClassAttr bodyClass" custom="bodyAttributes">John</td>
            </tr>
            <tr>
            <td class="bodyClassAttr bodyClass" custom="bodyAttributes">Mary</td>
            </tr>
            </tbody>
            </table>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            GridView::widget()
                ->columns(
                    new DataColumn(
                        'name',
                        columnAttributes: [
                            'custom' => 'columnAttributes',
                            'class' => 'columnClassAttr',
                        ],
                        headerAttributes: [
                            'custom' => 'headerAttributes',
                            'class' => 'headerClassAttr',
                        ],
                        bodyAttributes: [
                            'custom' => 'bodyAttributes',
                            'class' => ['bodyClassAttr'],
                        ],
                        columnClass: 'columnClass',
                        headerClass: 'headerClass',
                        bodyClass: 'bodyClass'
                    ),
                )
                ->columnGrouping()
                ->dataReader(new IterableDataReader($this->data))
                ->render()
        );
    }

    public function testFilterEmptyDefault(): void
    {
        $data = [
            ['id' => 1, 'status' => 0],
            ['id' => 2, 'status' => 1],
        ];

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'status',
                    filter: true,
                    filterEmpty: true
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('<input type="text" name="status"', $output);
        $this->assertStringContainsString('<td>&nbsp;</td>', $output);
        $this->assertStringContainsString('<td>1</td>', $output);
    }

    public function testFilterEmptyCustom(): void
    {
        $data = [
            ['id' => 1, 'status' => 0],
            ['id' => 2, 'status' => 1],
        ];

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'status',
                    filter: true,
                    filterEmpty: fn($value): bool => $value === 0
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('<input type="text" name="status"', $output);
        $this->assertStringContainsString('<td>&nbsp;</td>', $output);
        $this->assertStringContainsString('<td>1</td>', $output);
    }

    public function testDropdownFilter(): void
    {
        $data = [
            ['id' => 1, 'status' => 'active'],
            ['id' => 2, 'status' => 'inactive'],
        ];

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'status',
                    filter: ['active' => 'Active', 'inactive' => 'Inactive']
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('<select name="status"', $output);
        $this->assertStringContainsString('<option value="active">Active</option>', $output);
        $this->assertStringContainsString('<option value="inactive">Inactive</option>', $output);
    }

    public function testHeaderWithoutProperty(): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    content: fn($data) => $data['id']
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();

        $this->assertStringContainsString('<th></th>', $output);
    }

    public function testHeaderWithCustomText(): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    property: 'id',
                    header: '<b>ID</b>',
                    encodeHeader: false
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();

        $this->assertStringContainsString('<th><b>ID</b></th>', $output);
    }

    public function testHeaderWithDefaultText(): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn('user_name'),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();

        $this->assertStringContainsString('<th>User_name</th>', $output);
    }

    public function testColumnAndHeaderClass(): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'id',
                    columnClass: 'column-class',
                    headerClass: 'header-class'
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString('<col class="column-class"', $output);
        $this->assertStringContainsString('<th class="header-class"', $output);
    }

    public function testFilterWithValidationError(): void
    {
        $data = [
            ['id' => 1, 'email' => 'test@example.com'],
            ['id' => 2, 'email' => 'invalid'],
        ];

        $_GET['email'] = 'not-an-email';

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'email',
                    filter: true,
                    filterValidation: new \Yiisoft\Validator\Rule\Email()
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('name="email"', $output);
        $this->assertStringContainsString('test@example.com', $output);
        $this->assertStringContainsString('invalid', $output);

        unset($_GET['email']);
    }

    public function testSortingWithField(): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'name',
                    field: 'user.name',
                    withSorting: true
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();

        $this->assertStringContainsString('<th>Name</th>', $output);
        $this->assertStringContainsString('John', $output);
        $this->assertStringContainsString('Mary', $output);
    }

    public function testCustomFilterFactory(): void
    {
        $data = [
            ['id' => 1, 'status' => 'active'],
            ['id' => 2, 'status' => 'inactive'],
        ];

        $_GET['status'] = 'active';

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'status',
                    filter: true,
                    filterFactory: new \Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory()
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('<input type="text" name="status"', $output);
        $this->assertStringContainsString('active', $output);

        unset($_GET['status']);
    }

    public function testContentWithNullValue(): void
    {
        $data = [
            ['id' => 1, 'name' => null],
            ['id' => 2, 'name' => 'Mary'],
        ];

        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'name',
                    content: fn($data) => $data['name'] ?? 'N/A'
                ),
            )
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, 10))
            ->render();

        $this->assertStringContainsString('<td>N/A</td>', $output);
        $this->assertStringContainsString('<td>Mary</td>', $output);
    }

    public static function dataEncodeContent(): array
    {
        return [
            ['John &gt;', null, null],
            ['John &gt;', null, true],
            ['John >', null, false],
            ['123', 123, null],
            ['123', 123, true],
            ['123', 123, false],
            ['20.12', 20.12, null],
            ['20.12', 20.12, true],
            ['20.12', 20.12, false],
            ['1 &gt; 2', '1 > 2', null],
            ['1 &gt; 2', '1 > 2', true],
            ['1 > 2', '1 > 2', false],
            ['1 > 2', NoEncode::string('1 > 2'), null],
            ['1 &gt; 2', NoEncode::string('1 > 2'), true],
            ['1 > 2', NoEncode::string('1 > 2'), false],
            ['1 &gt; 2', static fn() => '1 > 2', null],
            ['1 &gt; 2', static fn() => '1 > 2', true],
            ['1 > 2', static fn() => '1 > 2', false],
            ['1 > 2', static fn() => NoEncode::string('1 > 2'), null],
            ['1 &gt; 2', static fn() => NoEncode::string('1 > 2'), true],
            ['1 > 2', static fn() => NoEncode::string('1 > 2'), false],
        ];
    }

    #[DataProvider('dataEncodeContent')]
    public function testEncodeContent(string $expected, mixed $content, ?bool $encodeContent): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'name',
                    content: $content,
                    encodeContent: $encodeContent,
                ),
            )
            ->dataReader(
                new IterableDataReader([
                    ['id' => 1, 'name' => 'John >'],
                ])
            )
            ->render();

        $this->assertStringContainsString($expected, $output);
    }

    #[TestWith(['1 > 2', null])]
    #[TestWith(['1 &gt; 2', true])]
    #[TestWith(['1 > 2', false])]
    public function testEncodeContentWithNoEncodeInData(string $expected, ?bool $encodeContent): void
    {
        $output = GridView::widget()
            ->columns(
                new DataColumn(
                    'name',
                    encodeContent: $encodeContent,
                ),
            )
            ->dataReader(
                new IterableDataReader([
                    ['id' => 1, 'name' => NoEncode::string('1 > 2')],
                ])
            )
            ->render();

        $this->assertStringContainsString($expected, $output);
    }
}
