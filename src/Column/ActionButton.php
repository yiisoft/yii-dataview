<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Stringable;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * `ActionButton` represents a button in an action column of a grid.
 *
 * @psalm-type TContentClosure = Closure(array|object $data, DataContext $context): (string|Stringable)
 * @psalm-type TUrlClosure = Closure(array|object $data, DataContext $context): string
 * @psalm-type TAttributesClosure = Closure(array|object $data, DataContext $context): array
 * @psalm-type TClassClosure = Closure(array|object $data, DataContext $context): (array<array-key, string|null>|string|null)
 */
final class ActionButton
{
    /**
     * @param Closure|string|Stringable $content Button content. If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string|Stringable`.
     * @param Closure|string|null $url Button URL. If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string`.
     * @param array|Closure|null $attributes HTML attributes. If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): array`.
     * @param array|Closure|false|string|null $class CSS class(es). If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string|array<array-key,string|null>|null`.
     * @param string|null $title Button title attribute.
     * @param bool $overrideAttributes Whether to override default attributes with custom ones instead of merging.
     *
     * @psalm-param TContentClosure|string|Stringable $content
     * @psalm-param TUrlClosure|string|null $url
     * @psalm-param TAttributesClosure|array|null $attributes
     * @psalm-param TClassClosure|array<array-key,string|null>|false|string|null $class
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
