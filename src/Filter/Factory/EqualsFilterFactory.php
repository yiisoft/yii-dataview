<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsFilterFactory implements FilterFactoryInterface
{
    public function create(string $property, string $value): ?FilterInterface
    {
        if (empty($value)) {
            return null;
        }

        return new Equals($property, $value);
    }
}
