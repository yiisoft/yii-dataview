<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * `SerialColumn` displays a column of row numbers (1-based).
 */
final class SerialColumn implements ColumnInterface
{
    public function __construct(
        private ?string $header = null,
        private ?string $footer = null,
        private array $columnAttributes = [],
        private array $bodyAttributes = [],
        private bool $visible = true,
    ) {
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function getColumnAttributes(): array
    {
        return $this->columnAttributes;
    }

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
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
