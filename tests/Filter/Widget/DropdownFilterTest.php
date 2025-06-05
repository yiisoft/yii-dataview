<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Filter\Widget;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Filter\Widget\Context;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Tests\Support\IntegerEnum;
use Yiisoft\Yii\DataView\Tests\Support\StringEnum;

/**
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\Context
 * @covers \Yiisoft\Yii\DataView\Filter\Widget\FilterWidget
 */
final class DropdownFilterTest extends TestCase
{
    public function testRenderFilter(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->optionsData([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
        $context = new Context('status', 'active', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('name="status"', $html);
        $this->assertStringContainsString('form="filter-form"', $html);
        $this->assertStringContainsString('onChange="this.form.submit()"', $html);
        $this->assertStringContainsString('value="active"', $html);
        $this->assertStringContainsString('selected', $html);
    }

    public function testRenderFilterWithoutValue(): void
    {
        $filter = new DropdownFilter();
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('name="status"', $html);
        $this->assertStringContainsString('form="filter-form"', $html);
        $this->assertStringContainsString('onChange="this.form.submit()"', $html);
        $this->assertStringNotContainsString('value="', $html);
    }

    public function testOptionsData(): void
    {
        $filter = new DropdownFilter();
        $options = [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ];

        $filter = $filter->optionsData($options);
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('>Active<', $html);
        $this->assertStringContainsString('>Inactive<', $html);
        $this->assertStringContainsString('value="active"', $html);
        $this->assertStringContainsString('value="inactive"', $html);
    }

    public function testAddAttributes(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->addAttributes(['class' => 'custom-select', 'data-test' => 'value']);

        $context = new Context('status', null, 'filter-form');
        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('class="custom-select"', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }

    public function testAttributes(): void
    {
        $filter = new DropdownFilter();
        $filter = $filter->attributes(['class' => 'new-select', 'required' => true]);

        $context = new Context('status', null, 'filter-form');
        $html = $filter->renderFilter($context);

        $this->assertStringContainsString('class="new-select"', $html);
        $this->assertStringContainsString('required', $html);
    }

    public static function dataAddClass(): array
    {
        return [
            ['<select class="main" name="status"', []],
            ['<select class="main" name="status"', ['main']],
            ['<select class="main bold" name="status"', ['bold']],
            ['<select class="main italic bold" name="status"', ['italic bold']],
            ['<select class="main italic bold" name="status"', ['italic', 'bold']],
            ['<select class="main test-class-1 test-class-2" name="status"', [StringEnum::TEST_CLASS_1, StringEnum::TEST_CLASS_2]],
            [
                '<select class="main test-class-1 test-class-2" name="status"',
                [IntegerEnum::A, StringEnum::TEST_CLASS_1, IntegerEnum::B, StringEnum::TEST_CLASS_2],
            ],
        ];
    }

    #[DataProvider('dataAddClass')]
    public function testAddClass(string $expected, array $class): void
    {
        $context = new Context('status', null, 'filter-form');
        $html = DropdownFilter::widget()
            ->addClass('main')
            ->addClass(...$class)
            ->renderFilter($context);
        $this->assertStringContainsString($expected, $html);
    }

    public static function dataNewClass(): array
    {
        return [
            ['<select name="status"', null],
            ['<select class name="status"', ''],
            ['<select class="red" name="status"', 'red'],
        ];
    }

    #[DataProvider('dataNewClass')]
    public function testNewClass(string $expected, ?string $class): void
    {
        $context = new Context('status', null, 'filter-form');
        $html = DropdownFilter::widget()
            ->addClass($class)
            ->renderFilter($context);
        $this->assertStringContainsString($expected, $html);
    }

    public static function dataClass(): array
    {
        return [
            ['<select name="status"', []],
            ['<select name="status"', [null]],
            ['<select class name="status"', ['']],
            ['<select class="main" name="status"', ['main']],
            ['<select class="main bold" name="status"', ['main bold']],
            ['<select class="main bold" name="status"', ['main', 'bold']],
            ['<select class="test-class-1 test-class-2" name="status"', [StringEnum::TEST_CLASS_1, StringEnum::TEST_CLASS_2]],
            [
                'class="test-class-1 test-class-2" name="status"',
                [IntegerEnum::A, StringEnum::TEST_CLASS_1, IntegerEnum::B, StringEnum::TEST_CLASS_2],
            ],
        ];
    }

    #[DataProvider('dataClass')]
    public function testClass(string $expected, array $class): void
    {
        $context = new Context('status', null, 'filter-form');
        $html = DropdownFilter::widget()
            ->class('red')
            ->class(...$class)
            ->renderFilter($context);
        $this->assertStringContainsString($expected, $html);
    }
}
