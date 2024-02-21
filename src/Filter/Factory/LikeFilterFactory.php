<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\FilterInterface;

final class LikeFilterFactory implements FilterFactoryInterface
{
    public function create(string $property, string $value): ?FilterInterface
    {
        if (empty($value)) {
            return null;
        }

        return new Like($property, $value);
    }
}
