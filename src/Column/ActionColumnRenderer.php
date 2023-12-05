<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use InvalidArgumentException;
use LogicException;
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
     * @var UrlCreator|null
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
        private readonly ?string $defaultTemplate = null,
        ?array $defaultButtons = null,
    ) {
        $this->defaultUrlCreator = $defaultUrlCreator;

        $this->defaultButtons = $defaultButtons ?? [
            'view' => static fn(string $url): string => Html::a(
                Html::span('🔎'),
                $url,
                [
                    'name' => 'view',
                    'role' => 'button',
                    'style' => 'text-decoration: none!important;',
                    'title' => 'View',
                ],
            )->render(),
            'update' => static fn(string $url): string => Html::a(
                Html::span('✎'),
                $url,
                [
                    'name' => 'update',
                    'role' => 'button',
                    'style' => 'text-decoration: none!important;',
                    'title' => 'Update',
                ],
            )->render(),
            'delete' => static fn(string $url): string => Html::a(
                Html::span('❌'),
                $url,
                [
                    'name' => 'delete',
                    'role' => 'button',
                    'style' => 'text-decoration: none!important;',
                    'title' => 'Delete',
                ],
            )->render(),
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
                        $url = $this->createUrl($name, $context);
                        return $buttons[$name]($url);
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

    private function createUrl(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $urlCreator = $column->getUrlCreator() ?? $this->defaultUrlCreator;
        if ($urlCreator === null) {
            throw new LogicException('Do not set URL creator.');
        }

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
    private function getTemplate(ActionColumn $column, array $buttons): string
    {
        if ($column->template !== null) {
            return $column->template;
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
