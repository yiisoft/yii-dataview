<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

use Closure;
use Stringable;
use Yiisoft\Html\NoEncode;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

/**
 * `ActionButton` represents a button in an action column of a grid.
 *
 * ```php
 * // Simple button with static content and URL
 * $button = new ActionButton(
 *     content: '✎',
 *     url: '/edit',
 *     title: 'Edit',
 * );
 *
 * // Button with dynamic URL based on row data
 * $button = new ActionButton(
 *     content: '❌',
 *     url: static function (array|object $data, DataContext $context): string {
 *         return '/delete/' . $data['id'];
 *     },
 *     title: 'Delete',
 * );
 *
 * // Button with dynamic content and attributes
 * $button = new ActionButton(
 *     content: static function (array|object $data, DataContext $context): string {
 *         return $data['active'] ? 'Deactivate' : 'Activate';
 *     },
 *     url: '/toggle',
 *     attributes: ['data-confirm' => 'Are you sure?'],
 *     class: 'btn btn-sm',
 * );
 *
 * // Button with dynamic CSS classes
 * $button = new ActionButton(
 *     content: '🔎',
 *     url: '/view',
 *     title: 'View',
 *     class: static function (array|object $data, DataContext $context): string {
 *         return $data['active'] ? 'btn-success' : 'btn-secondary';
 *     },
 * );
 * ```
 */
final class ActionButton
{
    /**
     * @param Closure|string|Stringable $content Button content. To prevent HTML encoding use {@see NoEncode::string()}.
     * If closure is used, its signature is: `function(array|object $data, DataContext $context): string|Stringable`.
     * @param Closure|string|null $url Button URL. If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string`.
     * @param array|Closure|null $attributes HTML attributes. If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): array`.
     * @param array|Closure|false|string|null $class CSS class(es). If closure is used, its signature is:
     * `function(array|object $data, DataContext $context): string|array<string|null>|null`.
     * @param string|null $title Button title attribute.
     * @param bool $overrideAttributes Whether to override default attributes with custom ones instead of merging.
     *
     * @template TData as array|object
     * @psalm-param (Closure(TData, DataContext): string)|string|null $url
     * @psalm-param (Closure(TData, DataContext): array)|array|null $attributes
     * @psalm-param (Closure(TData, DataContext): (array<string|null>|string|null))|array<string|null>|false|string|null $class
     * @psalm-param (Closure(TData, DataContext): (string|Stringable))|string|Stringable $content
     */
    public function __construct(
        public readonly Closure|string|Stringable $content = '',
        public readonly Closure|string|null $url = null,
        public readonly Closure|array|null $attributes = null,
        public readonly Closure|string|array|false|null $class = false,
        public readonly ?string $title = null,
        public readonly bool $overrideAttributes = false,
    ) {}
}
