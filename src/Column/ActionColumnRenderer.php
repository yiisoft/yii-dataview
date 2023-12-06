<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use InvalidArgumentException;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * @psalm-import-type UrlCreator from ActionColumn
 * @psalm-import-type ButtonRenderer from ActionColumn
 */
final class ActionColumnRenderer implements ColumnRendererInterface
{
    /**
     * @var UrlCreator
     */
    private $defaultUrlCreator;

    /**
     * @psalm-var array<string,ButtonRenderer>
     */
    private readonly array $defaultButtons;

    /**
     * @psalm-param UrlCreator|null $defaultUrlCreator
     * @psalm-param array<string,ButtonRenderer>|null $defaultButtons
     */
    public function __construct(
        ?callable $defaultUrlCreator = null,
        ?array $defaultButtons = null,
    ) {
        $this->defaultUrlCreator = $defaultUrlCreator ?? static fn(): string => '#';

        $this->defaultButtons = $defaultButtons ?? [
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

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell
            ->content($column->header ?? $context->translate('Actions'))
            ->addAttributes($column->headerAttributes);
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        return null;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $contentSource = $column->content;

        if ($contentSource !== null) {
            $content = (string)(is_callable($contentSource) ? $contentSource($context->data, $context) : $contentSource);
        } else {
            $buttons = $column->buttons ?? $this->defaultButtons;
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
                $this->getTemplate($column, $buttons, $context),
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
            $url = $closure($context->data, $context);
        } else {
            $url = $button->url;
        }

        if ($button->attributes instanceof Closure) {
            $closure = $button->attributes;
            $attributes = $closure($context->data, $context);
        } else {
            $attributes = $button->attributes ?? [];
        }
        if (!$button->overrideAttributes) {
            /** @var array $buttonAttributes */
            $buttonAttributes = $context->columnsConfigs[ActionColumn::class]['buttonAttributes'] ?? [];
            if (!empty($buttonAttributes)) {
                $attributes = array_merge($buttonAttributes, $attributes);
            }
        }

        if ($button->class instanceof Closure) {
            $closure = $button->class;
            $class = $closure($context->data, $context);
        } else {
            $class = $button->class;
        }

        /** @var array<array-key,string|null>|string|null $buttonClass */
        $buttonClass = $context->columnsConfigs[ActionColumn::class]['buttonClass'] ?? null;
        if ($class === false) {
            Html::addCssClass($attributes, $buttonClass);
        } else {
            if (!$button->overrideAttributes) {
                Html::addCssClass($attributes, $buttonClass);
            }
            Html::addCssClass($attributes, $class);
        }

        return (string)Html::a($content, $url, $attributes);
    }

    private function createUrl(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $urlCreator = $column->getUrlCreator() ?? $this->defaultUrlCreator;

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
     * @psalm-param array<string,ButtonRenderer> $buttons
     */
    private function getTemplate(ActionColumn $column, array $buttons, DataContext $context): string
    {
        if ($column->template !== null) {
            return $column->template;
        }

        /** @var string|null $defaultTemplate */
        $defaultTemplate = $context->columnsConfigs[ActionColumn::class]['template'] ?? null;
        if ($defaultTemplate !== null) {
            return $defaultTemplate;
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
