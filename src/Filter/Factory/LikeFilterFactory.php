<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\Filter\Like;

final class LikeFilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private readonly ?bool $caseSensitive = null,
    ) {
    }

    public function create(string $property, string $value): ?Like
    {
        if (empty($value)) {
            return null;
        }

        return new Like($property, $value, $this->caseSensitive);
    }
}
