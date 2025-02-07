<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Stringable;

/**
 * ActionButton represents a button in an action column of a grid.
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
     * @param array<array-key,string|null>|Closure|false|string|null $class CSS class(es). If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string|array<array-key,string|null>|null`.
     * @param string|null $title Button title attribute.
     * @param bool $overrideAttributes Whether to override default attributes with custom ones instead of merging.
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
