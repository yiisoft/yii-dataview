<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\FilterInterface;

interface FilterFactoryInterface
{
    public function create(string $property, string $value): ?FilterInterface;
}
