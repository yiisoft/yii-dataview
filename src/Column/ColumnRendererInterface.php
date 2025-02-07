<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * Column renderer purpose is to configure a {@see Cell grid cell} as a column, header, body, or footer
 * given a {@see ColumnInterface column definition}.
 */
interface ColumnRendererInterface
{
    /**
     * Configure a column cell.
     *
     * @param ColumnInterface $column Column definition.
     * @param Cell $cell Cell to configure.
     * @param GlobalContext $context Context data and dependencies.
     * @return Cell Configured cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;

    /**
     * Configure a header cell.
     *
     * @param ColumnInterface $column Column definition.
     * @param Cell $cell Cell to configure.
     * @param HeaderContext $context Context data and dependencies.
     * @return Cell|null Configured cell or `null` if header should not be rendered.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell;

    /**
     * Configure a body cell.
     *
     * @param ColumnInterface $column Column definition.
     * @param Cell $cell Cell to configure.
     * @param DataContext $context Context data and dependencies.
     * @return Cell Configured cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell;

    /**
     * Configure a footer cell.
     *
     * @param ColumnInterface $column Column definition.
     * @param Cell $cell Cell to configure.
     * @param GlobalContext $context Context data and dependencies.
     * @return Cell Configured cell.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell;
}
