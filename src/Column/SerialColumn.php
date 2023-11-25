<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * `SerialColumn` displays a column of row numbers (1-based).
 */
final class SerialColumn implements ColumnInterface
{
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
