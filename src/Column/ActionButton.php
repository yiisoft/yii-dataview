<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Stringable;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * @psalm-type ContentClosure = Closure(array|object,DataContext):string|Stringable
 * @psalm-type UrlClosure = Closure(array|object,DataContext):string
 * @psalm-type AttributesClosure = Closure(array|object,DataContext):array
 * @psalm-type ClassClosure = Closure(array|object,DataContext):string|array<array-key,string|null>|null
 */
final class ActionButton
{
    /**
     * @psalm-param UrlClosure|string|null $url
     * @psalm-param string|array<array-key,string|null>|null $class
     */
    public function __construct(
        public readonly Closure|string|Stringable $content = '',
        public readonly Closure|string|null $url = null,
        public readonly Closure|array|null $attributes = null,
        public readonly Closure|string|array|null $class = null,
        public readonly bool $overrideAttributes = false,
    ) {
    }
}
