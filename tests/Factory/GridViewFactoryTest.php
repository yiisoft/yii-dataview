<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Factory;

use RuntimeException;
use stdClass;
use Yiisoft\Yii\DataView\Columns\ActionColumn;
use Yiisoft\Yii\DataView\Columns\CheckboxColumn;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\Columns\RadioButtonColumn;
use Yiisoft\Yii\DataView\Columns\SerialColumn;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class GridViewFactoryTest extends TestCase
{
    public function testCreateActionColumnClass(): void
    {
        $config = ['__class' => ActionColumn::class];

        $column = $this->gridViewFactory->createColumnClass($config);
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testCreateCheckboxClass(): void
    {
        $config = ['__class' => CheckboxColumn::class];

        $column = $this->gridViewFactory->createColumnClass($config);
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testCreateDataColumnClass(): void
    {
        $config = ['__class' => DataColumn::class];

        $column = $this->gridViewFactory->createColumnClass($config);
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testCreateRadioButtonColumnClass(): void
    {
        $config = ['__class' => RadioButtonColumn::class];

        $column = $this->gridViewFactory->createColumnClass($config);
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testCreateSerialColumnClass(): void
    {
        $config = ['__class' => SerialColumn::class];

        $column = $this->gridViewFactory->createColumnClass($config);
        $this->assertInstanceOf(Column::class, $column);
    }

    public function testException(): void
    {
        $config = ['__class' => stdClass::class];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The "stdClass" is not an instance of the "Yiisoft\Yii\DataView\Columns\Column".'
        );
        $column = $this->gridViewFactory->createColumnClass($config);
    }
}
