<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * An interface for a grid column.
 */
interface ColumnInterface
{
    /**
     * A matching {@see ColumnRendererInterface renderer} name.
     *
     * @return string A column renderer name.
     *
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string;

    /**
     * @return bool Whether column should be displayed.
     */
    public function isVisible(): bool;
}
