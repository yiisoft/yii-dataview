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
    public function testBase(): void
    {
        $filter = DropdownFilter::widget()->optionsData([
            'active' => 'Active',
            'inactive' => 'Inactive <',
        ]);
        $context = new Context('status', 'active', 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            <<<HTML
            <select name="status" form="filter-form" onChange="this.form.submit()">
            <option value></option>
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive &lt;</option>
            </select>
            HTML,
            $html,
        );
    }

    public function testWithoutValue(): void
    {
        $filter = DropdownFilter::widget()->optionsData([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            <<<HTML
            <select name="status" form="filter-form" onChange="this.form.submit()">
            <option value></option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            </select>
            HTML,
            $html,
        );
    }

    public function testAddAttributes(): void
    {
        $filter = DropdownFilter::widget()
            ->addAttributes(['data-test' => 'value'])
            ->addAttributes(['class' => 'custom-select']);
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            <<<HTML
            <select class="custom-select" name="status" form="filter-form" data-test="value" onChange="this.form.submit()">
            <option value></option>
            </select>
            HTML,
            $html,
        );
    }

    public function testAttributes(): void
    {
        $filter = DropdownFilter::widget()
            ->attributes(['data-test' => 'value'])
            ->attributes(['class' => 'custom-select']);
        $context = new Context('status', null, 'filter-form');

        $html = $filter->renderFilter($context);

        $this->assertSame(
            <<<HTML
            <select class="custom-select" name="status" form="filter-form" onChange="this.form.submit()">
            <option value></option>
            </select>
            HTML,
            $html,
        );
    }

    public static function dataAddClass(): array
    {
        return [
            ['<select class="main" name="status"', []],
            ['<select class="main" name="status"', ['main']],
            ['<select class="main bold" name="status"', ['bold']],
            ['<select class="main italic bold" name="status"', ['italic bold']],
            ['<select class="main italic bold" name="status"', ['italic', 'bold']],
            ['<select class="main red green" name="status"', [StringEnum::RED, StringEnum::GREEN]],
            [
                '<select class="main red green" name="status"',
                [IntegerEnum::A, StringEnum::RED, IntegerEnum::B, StringEnum::GREEN],
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

    public static function dataClass(): array
    {
        return [
            ['<select name="status"', []],
            ['<select name="status"', [null]],
            ['<select class name="status"', ['']],
            ['<select class="main" name="status"', ['main']],
            ['<select class="main bold" name="status"', ['main bold']],
            ['<select class="main bold" name="status"', ['main', 'bold']],
            ['<select class="red green" name="status"', [StringEnum::RED, StringEnum::GREEN]],
            [
                'class="red green" name="status"',
                [IntegerEnum::A, StringEnum::RED, IntegerEnum::B, StringEnum::GREEN],
            ],
        ];
    }

    #[DataProvider('dataClass')]
    public function testClass(string $expected, array $class): void
    {
        $context = new Context('status', null, 'filter-form');

        $html = DropdownFilter::widget()
            ->class('test-class')
            ->class(...$class)
            ->renderFilter($context);

        $this->assertStringContainsString($expected, $html);
    }

    public function testImmutability(): void
    {
        $filter = DropdownFilter::widget();
        $this->assertNotSame($filter, $filter->optionsData([]));
        $this->assertNotSame($filter, $filter->addAttributes([]));
        $this->assertNotSame($filter, $filter->attributes([]));
        $this->assertNotSame($filter, $filter->addClass());
        $this->assertNotSame($filter, $filter->class());
    }
}
