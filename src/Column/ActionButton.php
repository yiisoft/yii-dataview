<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Stringable;

final class ActionButton
{
    /**
     * @param Closure|string|Stringable $content Closure signature: Closure(array|object,DataContext): string|Stringable
     * @param Closure|string|null $url Closure signature: Closure(array|object, DataContext): string
     * @param array|Closure|null $attributes Closure signature: Closure(array|object,DataContext):array
     * @param array<array-key,string|null>|Closure|false|string|null $class Closure signature: Closure(array|object,DataContext):string|array<array-key,string|null>|null
     */
    public function __construct(
        public readonly Closure|string|Stringable $content = '',
        public readonly Closure|string|null $url = null,
        public readonly Closure|array|null $attributes = null,
        public readonly Closure|string|array|null|false $class = false,
        public readonly string|null $title = null,
        public readonly bool $overrideAttributes = false,
    ) {
    }
}
