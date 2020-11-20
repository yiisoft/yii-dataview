<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Columns;

use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Columns\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

/**
 * @group grid
 */
class CheckboxColumnTest extends TestCase
{
    /**
     * @dataProvider inputName()
     *
     * @param string $name
     * @param string $expectedPart
     */
    public function testInputName(string $name, string $expectedPart): void
    {
        $column = CheckboxColumn::widget()
            ->name($name)
            ->grid($this->getGrid());
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
        $column = CheckboxColumn::widget()
            ->grid($this->getGrid());
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $column = CheckboxColumn::widget()
            ->checkboxOptions(['value' => 42])
            ->grid($this->getGrid());
        $this->assertStringNotContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 1, 0));

        $column = CheckboxColumn::widget()
            ->checkboxOptions(
                static function ($model, $key, $index, $column) {
                    return [];
                }
            )
            ->grid($this->getGrid());
        $this->assertStringContainsString('value="1"', $column->renderDataCell([], 1, 0));
        $this->assertStringContainsString('value="42"', $column->renderDataCell([], 42, 0));
        $this->assertStringContainsString('value="[1,42]"', $column->renderDataCell([], [1, 42], 0));

        $this->markTestIncomplete();
        $column = CheckboxColumn::widget()
            ->checkboxOptions(
                static function ($model, $key, $index, $column) {
                    return ['value' => 43];
                }
            )
            ->grid($this->getGrid());
        $this->assertStringContainsString('value="43"', $column->renderDataCell([], 1, 0));
    }

    public function testContent(): void
    {
        $column = CheckboxColumn::widget()
            ->content(
                static function ($model, $key, $index, $column) {
                    return '';
                }
            )
            ->grid($this->getGrid());
        $this->assertStringContainsString('<td></td>', $column->renderDataCell([], 1, 0));

        $column = CheckboxColumn::widget()
            ->content(
                static function ($model, $key, $index, $column) {
                    return Html::checkBox('checkBoxInput', false);
                }
            )->grid($this->getGrid());
        $this->assertStringContainsString(Html::checkBox('checkBoxInput', false), $column->renderDataCell([], 1, 0));
    }

    private function getGrid(): GridView
    {
        $dataReader = new IterableDataReader([]);

        return GridView::widget()
            ->dataReader($dataReader);
    }
}
