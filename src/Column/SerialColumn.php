<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * `SerialColumn` displays a column of row numbers (1-based).
 */
final class SerialColumn implements ColumnInterface, ColumnRendererInterface
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

    public function getRenderer(): self
    {
        return $this;
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->getColumnAttributes());
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($column->getHeader() ?? '#');
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        return null;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        return $cell
            ->addAttributes($column->getBodyAttributes())
            ->content((string)($context->getIndex() + 1));
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($this->getFooter() ?? '');
    }

    /**
     * @psalm-assert self $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof self) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    self::class,
                    $column::class
                )
            );
        }
    }
}
