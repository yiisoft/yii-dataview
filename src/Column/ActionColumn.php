<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * `ActionColumn` is a column for the {@see GridView} widget that displays buttons for viewing and manipulating
 * the items.
 *
 * @psalm-type UrlCreator = callable(string,DataContext):string
 * @psalm-type ButtonRenderer = ActionButton|callable(string):string
 */
final class ActionColumn implements ColumnInterface
{
    /**
     * @var UrlCreator|null URL creator callback.
     */
    private $urlCreator;

    /**
     * @param string|null $template The template used for composing each cell in the action column.
     * @param string|null $before Content to be prepended to the action column content.
     * @param string|null $after Content to be appended to the action column content.
     * @param mixed $urlConfig URL configuration for generating button URLs.
     * @param callable|null $urlCreator A callback that creates a button URL using the specified data information.
     * @param string|null $header The header cell content.
     * @param string|null $footer The footer cell content.
     * @param mixed $content The content to be rendered in each data cell.
     * @param array|null $buttons Array of buttons. Keys are button names. Values are either instances
     * of {@see ActionButton} or a callable with the following signature: `function(string $url): string`.
     * @param array|null $visibleButtons Array of button visibility rules.
     * @param array $columnAttributes HTML attributes for the column cells.
     * @param array $headerAttributes HTML attributes for the header cell.
     * @param array $bodyAttributes HTML attributes for the body cells.
     * @param array $footerAttributes HTML attributes for the footer cell.
     * @param bool $visible Whether the column is visible.
     *
     * @psalm-param UrlCreator|null $urlCreator
     * @psalm-param array<string, ButtonRenderer>|null $buttons
     * @psalm-param array<string, bool|Closure>|null $visibleButtons
     */
    public function __construct(
        public readonly ?string $template = null,
        public readonly ?string $before = null,
        public readonly ?string $after = null,
        public readonly mixed $urlConfig = null,
        ?callable $urlCreator = null,
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly mixed $content = null,
        public readonly ?array $buttons = null,
        public readonly ?array $visibleButtons = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $bodyAttributes = [],
        public readonly array $footerAttributes = [],
        private readonly bool $visible = true,
    ) {
        $this->urlCreator = $urlCreator;
    }

    /**
     * Get the URL creator callback.
     *
     * @return callable|null The URL creator callback.
     *
     * @psalm-return UrlCreator|null
     */
    public function getUrlCreator(): ?callable
    {
        return $this->urlCreator;
    }

    /**
     * Check if the column is visible.
     *
     * @return bool Whether the column should be rendered.
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Get the renderer class for this column.
     *
     * @return string The fully qualified class name of the renderer.
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string
    {
        return ActionColumnRenderer::class;
    }
}
