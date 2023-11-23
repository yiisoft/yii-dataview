<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * An interface for columns.
 */
interface ColumnInterface
{
    /**
     * A matching renderer name or an instance used for rendering this column.
     *
     * @return ColumnRendererInterface|string A column renderer name or an instance.
     */
    public function getRenderer(): string|ColumnRendererInterface;

    public function isVisible(): bool;
}
