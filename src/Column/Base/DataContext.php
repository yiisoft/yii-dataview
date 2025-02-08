<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Yii\DataView\Column\ColumnInterface;

final class DataContext
{
    /**
     * @param ReadableDataInterface|null $preparedDataReader The prepared data reader with applied filters, paginating
     * and sorting. `null` means that there is no data.
     * @param int $index 0-based index of the data item among the items currently rendered.
     */
    public function __construct(
        public readonly ?ReadableDataInterface $preparedDataReader,
        public readonly ColumnInterface $column,
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {
    }
}
