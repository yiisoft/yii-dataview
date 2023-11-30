<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * `ActionColumn` is a column for the {@see GridView} widget that displays buttons for viewing and manipulating
 * the items.
 */
final class ActionColumn implements ColumnInterface
{
    /**
     * @psalm-param array<string,Closure> $buttons
     * @psalm-param array<string,bool|Closure> $visibleButtons
     */
    public function __construct(
        public readonly string $primaryKey = 'id',
        public readonly ?string $template = null,
        public readonly ?string $routeName = null,
        public readonly array $urlParamsConfig = [],
        public readonly ?array $urlArguments = null,
        public readonly array $urlQueryParameters = [],
        public readonly ?Closure $urlCreator = null,
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly mixed $content = null,
        public readonly array $buttons = [],
        public readonly array $visibleButtons = [],
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $bodyAttributes = [],
        public readonly array $footerAttributes = [],
        private readonly bool $visible = true,
    ) {
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getRenderer(): string
    {
        return ActionColumnRenderer::class;
    }
}
