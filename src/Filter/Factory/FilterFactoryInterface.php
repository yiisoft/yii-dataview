<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\FilterInterface;

interface FilterFactoryInterface
{
    /**
     * @throws IncorrectValueException
     */
    public function create(string $property, string $value): ?FilterInterface;
}
