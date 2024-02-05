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
     * @return string A column renderer name.
     *
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string;

    public function isVisible(): bool;
}
