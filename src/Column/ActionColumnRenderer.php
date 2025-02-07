<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use InvalidArgumentException;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;

/**
 * ActionColumnRenderer renders action buttons in a grid column.
 *
 * This class is responsible for:
 * - Rendering action buttons with customizable content and appearance
 * - Managing button visibility and URL generation
 * - Handling template-based button layout
 *
 * @psalm-import-type UrlCreator from ActionColumn
 * @psalm-import-type ButtonRenderer from ActionColumn
 */
final class ActionColumnRenderer implements ColumnRendererInterface
{
    /**
     * @var callable URL creator callback.
     * @psalm-var UrlCreator
     */
    private $urlCreator;

    /**
     * @var array Default action buttons configuration.
     * @psalm-var array<array-key, ButtonRenderer>
     */
    private readonly array $buttons;

    /**
     * @param callable|null $urlCreator Callback for generating button URLs.
     * @param array|null $buttons Action buttons configuration.
     * @param array|string|null $buttonClass CSS class(es) for buttons.
     * @param array $buttonAttributes Default HTML attributes for buttons.
     * @param string|null $template Template for rendering buttons.
     * @param string|null $before Content to prepend to buttons.
     * @param string|null $after Content to append to buttons.
     *
     * @psalm-param UrlCreator|null $urlCreator
     * @psalm-param array<array-key, ButtonRenderer>|null $buttons
     * @psalm-param array<string, string>|string|null $buttonClass
     */
    public function __construct(
        ?callable $urlCreator = null,
        ?array $buttons = null,
        private readonly array|string|null $buttonClass = null,
        private readonly array $buttonAttributes = [],
        private readonly ?string $template = null,
        private readonly ?string $before = null,
        private readonly ?string $after = null,
    ) {
        $this->urlCreator = $urlCreator ?? static fn(): string => '#';

        $this->buttons = $buttons ?? [
            'view' => new ActionButton('🔎', attributes: ['title' => 'View']),
            'update' => new ActionButton('✎', attributes: ['title' => 'Update']),
            'delete' => new ActionButton('❌', attributes: ['title' => 'Delete']),
        ];
    }

    /**
     * Render the column container.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param GlobalContext $context Global rendering context.
     *
     * @return Cell The rendered cell.
     */
    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    /**
     * Render the column header.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param HeaderContext $context Header rendering context.
     *
     * @return Cell The rendered header cell.
     */
    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell
            ->content($column->header ?? $context->translate('Actions'))
            ->addAttributes($column->headerAttributes);
    }

    /**
     * Render a data cell in the column.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param DataContext $context Data rendering context.
     *
     * @return Cell The rendered data cell.
     */
    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $contentSource = $column->content;

        if ($contentSource !== null) {
            $content = (string)(is_callable($contentSource) ? $contentSource($context->data, $context) : $contentSource);
        } else {
            $buttons = $column->buttons ?? $this->buttons;
            $content = preg_replace_callback(
                '/{([\w\-\/]+)}/',
                function (array $matches) use ($column, $buttons, $context): string {
                    $name = $matches[1];

                    if (
                        isset($buttons[$name]) &&
                        $this->isVisibleButton(
                            $column,
                            $name,
                            $context->data,
                            $context->key,
                            $context->index,
                        )
                    ) {
                        return $this->renderButton($buttons[$name], $name, $context);
                    }

                    return '';
                },
                $this->getTemplate($column, $buttons),
            );
            $content = trim($content);
        }

        $content = "\n"
            . ($column->before ?? $this->before ?? '')
            . $content
            . ($column->after ?? $this->after ?? '')
            . "\n";

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content($content)
            ->encode(false);
    }

    /**
     * Render the column footer.
     *
     * @param ColumnInterface $column The column being rendered.
     * @param Cell $cell The cell to render.
     * @param GlobalContext $context Global rendering context.
     *
     * @return Cell The rendered footer cell.
     */
    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell->addAttributes($column->footerAttributes);
    }

    /**
     * Render a single action button.
     *
     * @param ActionButton|callable $button Button configuration or rendering callback.
     * @param string $name Button identifier.
     * @param DataContext $context Data rendering context.
     *
     * @return string The rendered button HTML.
     *
     * @psalm-param ButtonRenderer $button
     */
    private function renderButton(ActionButton|callable $button, string $name, DataContext $context): string
    {
        if (is_callable($button)) {
            $url = $this->createUrl($name, $context);
            return $button($url);
        }

        if ($button->content instanceof Closure) {
            $closure = $button->content;
            /** @var string $content */
            $content = $closure($context->data, $context);
        } else {
            $content = $button->content;
        }

        if ($button->url === null) {
            $url = $this->createUrl($name, $context);
        } elseif ($button->url instanceof Closure) {
            $closure = $button->url;
            /** @var string $url */
            $url = $closure($context->data, $context);
        } else {
            $url = $button->url;
        }

        if ($button->attributes instanceof Closure) {
            $closure = $button->attributes;
            /** @var array $attributes */
            $attributes = $closure($context->data, $context);
        } else {
            $attributes = $button->attributes ?? [];
        }
        if (!$button->overrideAttributes && !empty($this->buttonAttributes)) {
            $attributes = array_merge($this->buttonAttributes, $attributes);
        }

        if ($button->class instanceof Closure) {
            $closure = $button->class;
            /** @var array<array-key,string|null>|string|null $class */
            $class = $closure($context->data, $context);
        } else {
            $class = $button->class;
        }

        if ($class === false) {
            Html::addCssClass($attributes, $this->buttonClass);
        } else {
            if (!$button->overrideAttributes) {
                Html::addCssClass($attributes, $this->buttonClass);
            }
            Html::addCssClass($attributes, $class);
        }

        if ($button->title !== null) {
            $attributes['title'] = $button->title;
        }

        return (string) Html::a($content, $url, $attributes);
    }

    /**
     * Create a URL for an action button.
     *
     * @param string $action The action identifier.
     * @param DataContext $context Data rendering context.
     *
     * @return string The generated URL.
     */
    private function createUrl(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $urlCreator = $column->getUrlCreator() ?? $this->urlCreator;

        return $urlCreator($action, $context);
    }

    /**
     * Check if a button should be visible.
     *
     * @param ActionColumn $column The action column.
     * @param string $name Button identifier.
     * @param array|object $data Current data item.
     * @param mixed $key Current data key.
     * @param int $index Current row index.
     *
     * @return bool Whether the button should be visible.
     */
    private function isVisibleButton(
        ActionColumn $column,
        string $name,
        array|object $data,
        mixed $key,
        int $index
    ): bool {
        $visibleButtons = $column->visibleButtons;

        if ($visibleButtons === null) {
            return true;
        }

        $visibleValue = $visibleButtons[$name] ?? false;
        if (is_bool($visibleValue)) {
            return $visibleValue;
        }

        /** @var bool */
        return $visibleValue($data, $key, $index);
    }

    /**
     * Get the template for rendering buttons.
     *
     * @param ActionColumn $column The action column.
     * @param array $buttons Button configurations.
     *
     * @return string The template string.
     *
     * @psalm-param array<array-key, ButtonRenderer> $buttons
     */
    private function getTemplate(ActionColumn $column, array $buttons): string
    {
        if ($column->template !== null) {
            return $column->template;
        }

        if ($this->template !== null) {
            return $this->template;
        }

        $tokens = [];
        foreach ($buttons as $name => $_renderer) {
            $tokens[] = '{' . $name . '}';
        }

        return implode("\n", $tokens);
    }

    /**
     * Verify that the column is an ActionColumn.
     *
     * @param ColumnInterface $column The column to check.
     *
     * @throws InvalidArgumentException If the column is not an ActionColumn.
     *
     * @psalm-assert ActionColumn $column
     */
    private function checkColumn(ColumnInterface $column): void
    {
        if (!$column instanceof ActionColumn) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s", but "%s" given.',
                    self::class,
                    $column::class
                )
            );
        }
    }
}
