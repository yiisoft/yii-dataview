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
 * @psalm-import-type UrlCreator from ActionColumn
 * @psalm-import-type ButtonRenderer from ActionColumn
 */
final class ActionColumnRenderer implements ColumnRendererInterface
{
    /**
     * @psalm-var UrlCreator
     */
    private $urlCreator;

    /**
     * @psalm-var array<array-key, ButtonRenderer>
     */
    private readonly array $buttons;

    /**
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
    ) {
        $this->urlCreator = $urlCreator ?? static fn(): string => '#';

        $this->buttons = $buttons ?? [
            'view' => new ActionButton('🔎', attributes: ['title' => 'View']),
            'update' => new ActionButton('✎', attributes: ['title' => 'Update']),
            'delete' => new ActionButton('❌', attributes: ['title' => 'Delete']),
        ];
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->addAttributes($column->columnAttributes);
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell
            ->content($column->header ?? $context->translate('Actions'))
            ->addAttributes($column->headerAttributes);
    }

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
                        $this->isVisibleButton(
                            $column,
                            $name,
                            $context->data,
                            $context->key,
                            $context->index,
                        ) &&
                        isset($buttons[$name])
                    ) {
                        return $this->renderButton($buttons[$name], $name, $context);
                    }

                    return '';
                },
                $this->getTemplate($column, $buttons),
            );
            $content = trim($content);
        }

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content("\n" . $content . "\n")
            ->encode(false);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);

        if ($column->footer !== null) {
            $cell = $cell->content($column->footer);
        }

        return $cell->addAttributes($column->footerAttributes);
    }

    /**
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
        if (!$button->overrideAttributes) {
            if (!empty($this->buttonAttributes)) {
                $attributes = array_merge($this->buttonAttributes, $attributes);
            }
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

        return (string)Html::a($content, $url, $attributes);
    }

    private function createUrl(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $urlCreator = $column->getUrlCreator() ?? $this->urlCreator;

        return $urlCreator($action, $context);
    }

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
