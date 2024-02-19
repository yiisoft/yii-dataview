<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\UrlQueryReader;

interface FilterableColumnRendererInterface extends ColumnRendererInterface
{
    public function renderFilter(ColumnInterface $column, Cell $cell, FilterContext $context): ?Cell;

    public function makeFilter(ColumnInterface $column, UrlQueryReader $urlQueryReader): ?FilterInterface;
}
