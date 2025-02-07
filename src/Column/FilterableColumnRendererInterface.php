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
 * This interface extends the base ColumnRendererInterface to add filtering capabilities
 * to grid columns. Implementations handle both the rendering of filter inputs in the grid
 * and the creation of filter conditions based on user input.
 */
interface FilterableColumnRendererInterface extends ColumnRendererInterface
{
    /**
     * Renders the filter cell for a column.
     *
     * This method is responsible for:
     * - Creating the filter input UI (e.g., text input, dropdown)
     * - Handling filter validation errors
     * - Applying filter-specific styling and attributes
     *
     * @param ColumnInterface $column The column to render the filter for.
     * @param Cell $cell The cell container to render into.
     * @param FilterContext $context Context containing filter state and validation results.
     *
     * @return Cell|null The rendered filter cell, or null if filtering is not applicable.
     */
    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell;

    /**
     * Creates a filter condition based on user input.
     *
     * This method is responsible for:
     * - Converting user input into a filter condition
     * - Validating filter values
     * - Handling empty value checks
     * - Creating the appropriate filter type (e.g., equals, like, range)
     *
     * @param ColumnInterface $column The column to create the filter for.
     * @param MakeFilterContext $context Context containing filter parameters and validation state.
     *
     * @throws IncorrectValueException When the filter value is invalid or cannot be processed.
     * @return FilterInterface|null The created filter condition, or null if no filter should be applied.
     */
    public function makeFilter(ColumnInterface $column, MakeFilterContext $context): ?FilterInterface;
}
