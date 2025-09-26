<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

/**
 * Interface defining the contract for grid columns.
 *
 * This interface is the foundation for all grid columns in the data view system.
 * It defines the minimal requirements that any column must implement to be
 * properly rendered within a grid.
 */
interface ColumnInterface
{
    /**
     * Gets the fully qualified class name of the column's renderer.
     *
     * @return string The fully qualified class name of the renderer.
     *
     * @psalm-return class-string<ColumnRendererInterface>
     *
     * @see ColumnRendererInterface The interface that the renderer must implement.
     */
    public function getRenderer(): string;

    /**
     * Determines if the column should be rendered in the grid.
     *
     * @return bool Whether the column should be rendered.
     */
    public function isVisible(): bool;
}
