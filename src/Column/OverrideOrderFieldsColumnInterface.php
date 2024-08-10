<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

interface OverrideOrderFieldsColumnInterface
{
    /**
     * @return array<string,string>
     */
    public function getOverrideOrderFields(ColumnInterface $column): array;
}
