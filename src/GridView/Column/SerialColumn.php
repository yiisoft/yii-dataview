<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

/**
 * `SerialColumn` displays a column of sequential row numbers (1-based) in a grid view.
 */
final class SerialColumn implements ColumnInterface
{
    /**
     * Creates a new `SerialColumn` instance.
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

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getRenderer(): string
    {
        return SerialColumnRenderer::class;
    }
}
