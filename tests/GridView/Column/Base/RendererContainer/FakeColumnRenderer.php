<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base\RendererContainer;

use LogicException;
use Yiisoft\Yii\DataView\GridView\Column\Base\Cell;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\GridView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\GridView\Column\ColumnRendererInterface;

final class FakeColumnRenderer implements ColumnRendererInterface
{
    public function __construct(
        public readonly string $id = '',
        public readonly string $name = '',
    ) {}

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        throw new LogicException('Not implemented');
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        throw new LogicException('Not implemented');
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        throw new LogicException('Not implemented');
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        throw new LogicException('Not implemented');
    }
}
