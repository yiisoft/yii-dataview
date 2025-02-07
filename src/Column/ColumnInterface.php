<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * Interface defining the contract for grid columns.
 *
 * This interface is the foundation for all grid columns in the data view system.
 * It defines the minimal requirements that any column must implement to be
 * properly rendered within a grid.
 *
 * Features:
 * - Column visibility control
 * - Renderer association
 * - Separation of data and presentation logic
 *
 * Example implementation:
 * ```php
 * class CustomColumn implements ColumnInterface
 * {
 *     public function getRenderer(): string
 *     {
 *         return CustomColumnRenderer::class;
 *     }
 *
 *     public function isVisible(): bool
 *     {
 *         return true;
 *     }
 * }
 * ```
 */
interface ColumnInterface
{
    /**
     * Gets the fully qualified class name of the column's renderer.
     *
     * The renderer is responsible for the actual rendering of the column's:
     * - Header
     * - Body cells
     * - Footer
     * - Column container
     *
     * @return string The fully qualified class name of the renderer.
     *
     * @psalm-return class-string<ColumnRendererInterface>
     *
     * @see ColumnRendererInterface The interface that the renderer must implement
     */
    public function getRenderer(): string;

    /**
     * Determines if the column should be rendered in the grid.
     *
     * This method allows for dynamic visibility control based on:
     * - User permissions
     * - Data context
     * - Application state
     * - Custom business logic
     *
     * @return bool true if the column should be displayed, false otherwise.
     */
    public function isVisible(): bool;
}
