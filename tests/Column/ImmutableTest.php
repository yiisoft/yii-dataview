<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Yii\DataView\Column;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class ImmutableTest extends TestCase
{
    public function testActionsColumn(): void
    {
        $actionColumn = Column\ActionColumn::create();
        $this->assertNotSame($actionColumn, $actionColumn->buttons([]));
        $this->assertNotSame($actionColumn, $actionColumn->createDefaultButtons([]));
        $this->assertNotSame($actionColumn, $actionColumn->primaryKey(''));
        $this->assertNotSame($actionColumn, $actionColumn->template('{view}'));
        $this->assertNotSame($actionColumn, $actionColumn->urlArguments([]));
        $this->assertNotSame($actionColumn, $actionColumn->urlCreator(static fn () => ''));
        $this->assertNotSame($actionColumn, $actionColumn->urlEnabledArguments(false));
        $this->assertNotSame($actionColumn, $actionColumn->urlGenerator(Mock::urlGenerator([])));
        $this->assertNotSame($actionColumn, $actionColumn->urlName(''));
        $this->assertNotSame($actionColumn, $actionColumn->urlQueryParameters([]));
        $this->assertNotSame($actionColumn, $actionColumn->urlParamsConfig([]));
        $this->assertNotSame($actionColumn, $actionColumn->visibleButtons([]));
    }

    public function testCheckboxColumn(): void
    {
        $checkboxColumn = Column\CheckboxColumn::create();
        $this->assertNotSame($checkboxColumn, $checkboxColumn->notMultiple());
    }

    public function testColumn(): void
    {
        $column = $this->createColumn();
        $this->assertNotSame($column, $column->attributes([]));
        $this->assertNotSame($column, $column->content(static fn () => ''));
        $this->assertNotSame($column, $column->contentAttributes([]));
        $this->assertNotSame($column, $column->dataLabel(''));
        $this->assertNotSame($column, $column->emptyCell(''));
        $this->assertNotSame($column, $column->filterAttributes([]));
        $this->assertNotSame($column, $column->footer(''));
        $this->assertNotSame($column, $column->footerAttributes([]));
        $this->assertNotSame($column, $column->label(''));
        $this->assertNotSame($column, $column->labelAttributes([]));
        $this->assertNotSame($column, $column->name(''));
        $this->assertNotSame($column, $column->notVisible());
    }

    public function testDataColumn(): void
    {
        $column = Column\DataColumn::create();
        $this->assertNotSame($column, $column->attribute(''));
        $this->assertNotSame($column, $column->filter(''));
        $this->assertNotSame($column, $column->filterAttribute(''));
        $this->assertNotSame($column, $column->filterInputAttributes([]));
        $this->assertNotSame($column, $column->filterInputSelectItems([]));
        $this->assertNotSame($column, $column->filterInputSelectPrompt(''));
        $this->assertNotSame($column, $column->filterModelName(''));
        $this->assertNotSame($column, $column->filterType('text'));
        $this->assertNotSame($column, $column->filterValueDefault(null));
        $this->assertNotSame($column, $column->linkSorter(''));
        $this->assertNotSame($column, $column->notSorting());
        $this->assertNotSame($column, $column->value(null));
    }

    public function testSerialColumn(): void
    {
        $column = Column\SerialColumn::create();
        $this->assertNotSame($column, $column->offset(0));
    }

    private function createColumn(): Column\Column
    {
        return new class () extends Column\Column {
        };
    }
}
