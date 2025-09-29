<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumnRenderer;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\ValuePresenter\ValuePresenterInterface;

final class DataColumnTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createGridView([['name' => 'John', 'age' => 25]])
            ->columns(new DataColumn())
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr>
            <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td>&nbsp;</td>
            </tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testProperty(): void
    {
        $html = $this->createGridView([['name' => 'John', 'age' => 25]])
            ->columns(new DataColumn(property: 'name'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>John</td>
            HTML,
            $html,
        );
    }

    public function testHeader(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(new DataColumn(property: 'name', header: 'Full Name'))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Full Name</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    #[TestWith(['&lt;b&gt;Header&lt;/b&gt;', true])]
    #[TestWith(['<b>Header</b>', false])]
    public function testEncodeHeader(string $expected, bool $encode): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(new DataColumn(property: 'name', header: '<b>Header</b>', encodeHeader: $encode))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>$expected</th>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public static function dataContent(): iterable
    {
        yield 'string' => ['Custom Content', 'Custom Content'];
        yield 'stringable' => ['Custom Content', new StringableObject('Custom Content')];
        yield 'int' => ['42', 42];
        yield 'float' => ['3.14', 3.14];
        yield 'callable' => [
            'Name: John, Age: 25',
            static fn(array $data, DataContext $context) => 'Name: ' . $data['name'] . ', Age: ' . $data['age']
        ];
        yield 'value-presenter' => [
            'john',
            new class() implements ValuePresenterInterface {
                public function present(mixed $value): string
                {
                    return strtolower($value);
                }
            }
        ];
    }

    #[DataProvider('dataContent')]
    public function testContent(string $expected, mixed $content): void
    {
        $html = $this->createGridView([['name' => 'John', 'age' => 25]])
            ->columns(new DataColumn(property: 'name', content: $content))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>$expected</td>
            HTML,
            $html,
        );
    }

    #[TestWith(['&lt;b&gt;Bold&lt;/b&gt;', true])]
    #[TestWith(['<b>Bold</b>', false])]
    public function testEncodeContent(string $expected, bool $encode): void
    {
        $html = $this->createGridView([['content' => '<b>Bold</b>']])
            ->columns(new DataColumn(property: 'content', encodeContent: $encode))
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td>$expected</td>
            HTML,
            $html,
        );
    }

    public function testColumnAttributes(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new DataColumn(
                    property: 'name',
                    columnAttributes: ['class' => 'data-col']
                )
            )
            ->columnGrouping()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <colgroup>
            <col class="data-col">
            </colgroup>
            HTML,
            $html,
        );
    }

    public function testHeaderAttributes(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new DataColumn(
                    property: 'name',
                    headerAttributes: ['class' => 'header-class']
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <th class="header-class">Name</th>
            HTML,
            $html,
        );
    }

    public static function dataBodyAttributes(): iterable
    {
        yield 'array' => [
            '<td class="body-class">John</td>',
            ['class' => 'body-class']
        ];
        yield 'callable' => [
            '<td class="body-class-7">John</td>',
            static fn(array $data, DataContext $context) => ['class' => 'body-class-' . $data['id']]
        ];
    }

    #[DataProvider('dataBodyAttributes')]
    public function testBodyAttributes(string $expected, mixed $attributes): void
    {
        $html = $this->createGridView([['id' => 7, 'name' => 'John']])
            ->columns(
                new DataColumn(
                    property: 'name',
                    bodyAttributes: $attributes,
                )
            )
            ->render();

        $this->assertStringContainsString($expected, $html);
    }

    public static function dataBodyClass(): iterable
    {
        yield 'string' => [
            '<td class="body-class">John</td>',
            'body-class'
        ];
        yield 'array' => [
            '<td class="class1 class2">John</td>',
            ['class1', 'class2']
        ];
        yield 'callable' => [
            '<td class="body-class-7">John</td>',
            static fn(array $data, DataContext $context) => 'body-class-' . $data['id']
        ];
    }

    #[DataProvider('dataBodyClass')]
    public function testBodyClass(string $expected, mixed $bodyClass): void
    {
        $html = $this->createGridView([['id' => 7, 'name' => 'John']])
            ->columns(
                new DataColumn(
                    property: 'name',
                    bodyClass: $bodyClass,
                )
            )
            ->render();

        $this->assertStringContainsString($expected, $html);
    }

    public function testFooter(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(
                new DataColumn(
                    property: 'name',
                    footer: 'Footer Content'
                )
            )
            ->enableFooter()
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tfoot>
            <tr>
            <td>Footer Content</td>
            </tr>
            </tfoot>
            HTML,
            $html,
        );
    }

    public function testVisible(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->columns(new DataColumn(property: 'name', visible: false))
            ->render();

        $this->assertSame(
            <<<HTML
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr></tr>
            </tbody>
            </table>
            HTML,
            $html,
        );
    }

    public function testFilterArray(): void
    {
        $html = $this->createGridView([['status' => 'active']])
            ->filterFormId('FID')
            ->columns(
                new DataColumn(
                    property: 'status',
                    filter: ['active' => 'Active', 'inactive' => 'Inactive'],
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Status</th>
            </tr>
            <tr>
            <td><select name="status" form="FID" onChange="this.form.submit()">
            <option value></option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            </select></td>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testFilterArrayWithValue(): void
    {
        $html = $this->createGridView([['status' => 'active']])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['status' => 'inactive']))
            ->columns(
                new DataColumn(
                    property: 'status',
                    filter: ['active' => 'Active', 'inactive' => 'Inactive'],
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Status</th>
            </tr>
            <tr>
            <td><select name="status" form="FID" onChange="this.form.submit()">
            <option value></option>
            <option value="active">Active</option>
            <option value="inactive" selected>Inactive</option>
            </select></td>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testFilterTrue(): void
    {
        $html = $this->createGridView([['name' => 'John']])
            ->filterFormId('FID')
            ->columns(
                new DataColumn(
                    property: 'name',
                    filter: true,
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Name</th>
            </tr>
            <tr>
            <td><input type="text" name="name" form="FID"></td>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testFilterWidget(): void
    {
        $html = $this->createGridView([['email' => 'john@example.com']])
            ->filterFormId('FID')
            ->columns(
                new DataColumn(
                    property: 'email',
                    filter: TextInputFilter::widget()->addAttributes(['class' => 'red']),
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <thead>
            <tr>
            <th>Email</th>
            </tr>
            <tr>
            <td><input type="text" class="red" name="email" form="FID"></td>
            </tr>
            </thead>
            HTML,
            $html,
        );
    }

    public function testFilterValidation(): void
    {
        $html = $this->createGridView([['age' => 23]])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['age' => '105']))
            ->columns(
                new DataColumn(
                    property: 'age',
                    filter: true,
                    filterValidation: new Integer(min: 18, max: 99),
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <td><input type="text" name="age" value="105" form="FID"><div><div>Value must be no greater than 99.</div></div></td>
            HTML,
            $html,
        );
    }

    public function testFilterEmptyCallable(): void
    {
        $html = $this->createGridView([
            ['name' => 'Anna'],
            ['name' => 'Eva'],
        ])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['name' => 'anna']))
            ->columns(
                new DataColumn(
                    property: 'name',
                    filter: true,
                    filterEmpty: static fn(string $value) => $value === 'anna',
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>Anna</td>
            </tr>
            <tr>
            <td>Eva</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testFilterEmptyFalse(): void
    {
        $html = $this->createGridView([
            ['name' => 'Anna'],
            ['name' => 'Eva'],
            ['name' => ''],
        ])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['name' => '']))
            ->columns(
                new DataColumn(
                    property: 'name',
                    filter: true,
                    filterFactory: new EqualsFilterFactory(),
                    filterEmpty: false,
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>&nbsp;</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    public function testFilterData(): void
    {
        $html = $this->createGridView([
            ['name' => 'Anna'],
            ['name' => 'Eva'],
            ['name' => 'Anna 2'],
        ])
            ->filterFormId('FID')
            ->urlParameterProvider(new SimpleUrlParameterProvider(['name' => 'anna']))
            ->columns(
                new DataColumn(
                    property: 'name',
                    filter: true,
                )
            )
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <tbody>
            <tr>
            <td>Anna</td>
            </tr>
            <tr>
            <td>Anna 2</td>
            </tr>
            </tbody>
            HTML,
            $html,
        );
    }

    private function createGridView(array $data = []): GridView
    {
        $container = new Container(
            ContainerConfig::create()->withDefinitions([
                ValidatorInterface::class => Validator::class,
            ])
        );

        return (new GridView($container))
            ->layout('{items}')
            ->containerTag(null)
            ->dataReader(new IterableDataReader($data))
            ->addColumnRendererConfigs([
                DataColumnRenderer::class => [],
            ]);
    }
}
