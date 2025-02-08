<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * An interface for column renderers that implement {@see ColumnInterface}.
 *
 * @template TColumn as ColumnInterface
 */
interface ColumnRendererInterface
{
    /**
     * @psalm-param TColumn $column
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;

    /**
     * @psalm-param TColumn $column
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell;

    /**
     * @psalm-param TColumn $column
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell;

    /**
     * @psalm-param TColumn $column
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;
}
