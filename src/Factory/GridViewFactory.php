<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Factory;

use RuntimeException;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Factory\Factory;

final class GridViewFactory
{
    public function __construct(private Factory $factory)
    {
    }

    /**
     * Creates a DataColumn defined by config passed.
     *
     * @param array $config parameters for creating a widget.
     *
     * @throws RuntimeException
     */
    public function createColumnClass(array $config): Column
    {
        $columnClass = $this->factory->create($config);

        if (!($columnClass instanceof Column)) {
            throw new RuntimeException(
                sprintf(
                    'The "%s" is not an instance of the "%s".',
                    get_debug_type($columnClass),
                    Column::class
                )
            );
        }

        return $columnClass;
    }
}
