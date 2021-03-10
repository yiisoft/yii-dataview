<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Factory;

use RuntimeException;
use Yiisoft\Yii\DataView\Columns\Column;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Factory\Factory;

final class GridViewFactory
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Creates a DataColumn defined by config passed
     *
     * @param array $config parameters for creating a widget
     *
     * @throws RuntimeException if factory was not initialized
     * @throws InvalidConfigException
     *
     * @psalm-suppress MoreSpecificReturnType
     *
     * @return Column
     */
    public function createColumnClass(array $config): Column
    {
        $class = $this->factory->create($config);

        if (!($class instanceof Column)) {
            throw new RuntimeException(sprintf('The "%s" is not an instance of the "%s".', $class, Column::class));
        }

        return $class;
    }
}
