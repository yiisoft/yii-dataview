<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;

/**
 * Test renderer implementation.
 */
final class TestRenderer implements ColumnRendererInterface
{
    public function __construct(
        private readonly string $value = 'default',
        private readonly string $option = 'default',
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOption(): string
    {
        return $this->option;
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell;
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell
    {
        return $cell;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        return $cell;
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell;
    }
}
