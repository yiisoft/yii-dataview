<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Factory;

use RuntimeException;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Factory\Factory;

use function get_class;
use function gettype;
use function is_object;

final class GridViewFactory
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Creates a DataColumn defined by config passed.
     *
     * @param array $config parameters for creating a widget.
     *
     * @throws RuntimeException
     *
     * @return Column
     */
    public function createColumnClass(array $config): Column
    {
        $columnClass = $this->factory->create($config);

        if (!($columnClass instanceof Column)) {
            throw new RuntimeException(
                sprintf(
                    'The "%s" is not an instance of the "%s".',
                    is_object($columnClass) ? get_class($columnClass) : gettype($columnClass),
                    Column::class
                )
            );
        }

        return $columnClass;
    }
}
