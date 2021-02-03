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
     * @param string $expectedPart
     */
    public function testInputName(string $name, string $expectedPart): void
    {
        $column = $this->checkboxColumn
            ->name($name)
            ->grid($this->gridView);
        $this->assertStringContainsString($expectedPart, $column->renderHeaderCell());
    }

    public function inputName(): array
    {
        return [
            ['selection', 'name="selection_all"'],
            ['selections[]', 'name="selections_all"'],
            ['MyForm[grid1]', 'name="MyForm[grid1_all]"'],
            ['MyForm[grid1][]', 'name="MyForm[grid1_all]"'],
            ['MyForm[grid1][key]', 'name="MyForm[grid1][key_all]"'],
            ['MyForm[grid1][key][]', 'name="MyForm[grid1][key_all]"'],
        ];
    }

    public function testInputValue(): void
    {
        $column = $this->checkboxColumn
            ->grid($this->gridView);
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = $this->checkboxColumn
            ->checkboxOptions(['value' => 42])
            ->grid($this->gridView);
        $this->assertStringNotContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 1, 0));

        $column = $this->checkboxColumn
            ->checkboxOptions(static fn ($model, $key, $index, $column) => [])
            ->grid($this->gridView);
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = $this->checkboxColumn
            ->checkboxOptions(
                static function ($model, $key, $index, $column) {
                    return ['value' => 43];
                }
            )
            ->grid($this->gridView);
        $this->assertStringContainsString('value="43"', $column->renderDataCell([], 1, 0));
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
        $this->assertStringContainsString('<td></td>', $column->renderDataCell([], 1, 0));

        $column = $this->checkboxColumn
            ->content(
                static function ($model, $key, $index, $column) {
                    return Html::checkBox('checkBoxInput', false);
                }
            )->grid($this->gridView);
        $this->assertStringContainsString(Html::checkBox('checkBoxInput', false), $column->renderDataCell([], 1, 0));
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
