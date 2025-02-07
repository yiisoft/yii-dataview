<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * SerialColumn displays a column of sequential row numbers (1-based) in a grid view.
 *
 * This column is useful for:
 * - Displaying row numbers for reference
 * - Maintaining a consistent numbering sequence across pages
 * - Providing visual order in data presentation
 *
 * Example usage:
 * ```php
 * $column = new SerialColumn(
 *     header: '#',
 *     columnAttributes: ['class' => 'serial-column'],
 *     bodyAttributes: ['class' => 'text-center'],
 * );
 * ```
 */
final class SerialColumn implements ColumnInterface
{
    /**
     * Creates a new SerialColumn instance.
     *
     * @param string|null $header The header cell content. If null, no header will be rendered.
     * @param string|null $footer The footer cell content. If null, no footer will be rendered.
     * @param array $columnAttributes HTML attributes for all column cells.
     * @param array $bodyAttributes HTML attributes for the body cells.
     * @param bool $visible Whether the column is visible.
     */
    public function __construct(
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $bodyAttributes = [],
        private readonly bool $visible = true,
    ) {
    }

    /**
     * Checks if the column should be rendered.
     *
     * @return bool Whether the column is visible.
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Gets the renderer class for this column.
     *
     * @return string The fully qualified class name of the renderer.
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string
    {
        return SerialColumnRenderer::class;
    }
}
