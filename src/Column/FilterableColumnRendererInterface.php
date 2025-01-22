<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Filter\Factory\IncorrectValueException;

/**
 * @template TColumn as ColumnInterface
 * @extends ColumnRendererInterface<TColumn>
 */
interface FilterableColumnRendererInterface extends ColumnRendererInterface
{
    /**
     * @psalm-param TColumn $column
     */
    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell;

    /**
     * @throws IncorrectValueException
     *
     * @psalm-param TColumn $column
     */
    public function makeFilter(ColumnInterface $column, MakeFilterContext $context): ?FilterInterface;
}
