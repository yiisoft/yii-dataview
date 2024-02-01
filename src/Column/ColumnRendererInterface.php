<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * An interface for column renderers that implement {@see ColumnInterface}.
 */
interface ColumnRendererInterface
{
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell;

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell;

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;
}
