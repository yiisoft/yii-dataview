<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Columns;

use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class CheckboxColumnTest extends TestCase
{
    /**
     * @dataProvider inputName()
     *
     * @param string $name
     * @param string $html
     */
    public function testInputName(string $name, string $html): void
    {
        $column = $this->checkboxColumn->name($name)->grid($this->gridView);
        $this->assertSame($html, $column->renderHeaderCell());
    }

    public function inputName(): array
    {
        return [
            [
                'selection',
                '<th><input type="checkbox" class="select-on-check-all" name="selection_all" value="1"></th>',
            ],
            [
                'selections[]',
                '<th><input type="checkbox" class="select-on-check-all" name="selections_all" value="1"></th>',
            ],
            [
                'MyForm[grid1]',
                '<th><input type="checkbox" class="select-on-check-all" name="MyForm[grid1_all]" value="1"></th>'
            ],
            [
                'MyForm[grid1][]',
                '<th><input type="checkbox" class="select-on-check-all" name="MyForm[grid1_all]" value="1"></th>',
            ],
            [
                'MyForm[grid1][key]',
                '<th><input type="checkbox" class="select-on-check-all" name="MyForm[grid1][key_all]" value="1"></th>',
            ],
            [
                'MyForm[grid1][key][]',
                '<th><input type="checkbox" class="select-on-check-all" name="MyForm[grid1][key_all]" value="1"></th>',
            ],
        ];
    }

    public function testInputValue(): void
    {
        $column = $this->checkboxColumn->grid($this->gridView);

        $html = '<td><input type="checkbox" name="selection" value="1"></td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));

        $html= '<td><input type="checkbox" name="selection" value="42"></td>';
        $this->assertSame($html, $column->renderDataCell([], 42, 0));

        $html = '<td><input type="checkbox" name="selection" value="[1,42]"></td>';
        $this->assertSame($html, $column->renderDataCell([], [1, 42], 0));

        $column = $this->checkboxColumn->checkboxOptions(['value' => 42])->grid($this->gridView);

        $html = '<td><input type="checkbox" name="selection" value="42"></td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));

        $html = '<td><input type="checkbox" name="selection" value="42"></td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));

        $column = $this->checkboxColumn
            ->checkboxOptions(static fn ($model, $key, $index, $column) => [])
            ->grid($this->gridView);

        $html = '<td><input type="checkbox" name="selection" value="1"></td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));

        $html = '<td><input type="checkbox" name="selection" value="42"></td>';
        $this->assertSame($html, $column->renderDataCell([], 42, 0));

        $html = '<td><input type="checkbox" name="selection" value="[1,42]"></td>';
        $this->assertSame($html, $column->renderDataCell([], [1, 42], 0));

        $column = $this->checkboxColumn
            ->checkboxOptions(
                static function ($model, $key, $index, $column) {
                    return ['value' => 43];
                }
            )
            ->grid($this->gridView);

        $html = '<td><input type="checkbox" name="selection" value="43"></td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));
    }

    public function testContent(): void
    {
        $column = $this->checkboxColumn
            ->content(
                static function ($model, $key, $index, $column) {
                    return '';
                }
            )
            ->grid($this->gridView);

        $this->assertSame('<td></td>', $column->renderDataCell([], 1, 0));

        $column = $this->checkboxColumn
            ->content(
                static function ($model, $key, $index, $column) {
                    return Html::checkBox('checkBoxInput', false);
                }
            )->grid($this->gridView);

        $html = '<td>' . Html::checkBox('checkBoxInput', false) . '</td>';
        $this->assertSame($html, $column->renderDataCell([], 1, 0));
    }

    public function testCheckBoxException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "name" property must be set.');

        $column = $this->checkboxColumn
            ->name('')
            ->grid($this->gridView);
    }
}
