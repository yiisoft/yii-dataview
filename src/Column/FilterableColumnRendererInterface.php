<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Filter\Factory\IncorrectValueException;

/**
 * Interface for column renderers that support filtering functionality.
 *
 * @template TColumn as ColumnInterface
 * @extends ColumnRendererInterface<TColumn>
 */
interface FilterableColumnRendererInterface extends ColumnRendererInterface
{
    /**
     * Renders the filter cell for a column.
     *
     * @param ColumnInterface $column The column to render the filter for.
     * @psalm-param TColumn $column
     * @param Cell $cell The cell container to render into.
     * @param FilterContext $context Context containing filter state and validation results.
     *
     * @return Cell|null The rendered filter cell, or `null` if filtering is not applicable.
     */
    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell;

    /**
     * Creates a filter condition based on user input.
     *
     * @param ColumnInterface $column The column to create the filter for.
     * @psalm-param TColumn $column
     * @param MakeFilterContext $context Context containing filter parameters and validation state.
     *
     * @throws IncorrectValueException When the filter value is invalid or cannot be processed.
     * @return FilterInterface|null The created filter condition, or null if no filter should be applied.
     */
    public function makeFilter(ColumnInterface $column, MakeFilterContext $context): ?FilterInterface;
}
