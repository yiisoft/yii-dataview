<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use InvalidArgumentException;
use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

final class ActionColumnRenderer implements ColumnRendererInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
    ) {
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
            ->content($column->header ?? 'Actions')
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
            $content = (string)(is_callable($contentSource) ? $contentSource($context) : $contentSource);
        } else {
            $buttons = empty($column->buttons) ? $this->getDefaultButtons() : $column->buttons;
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
                        $url = $this->createUrl($column, $name, $context->data, $context->key);
                        return (string)$buttons[$name]($url);
                    }

                    return '';
                },
                $column->template
            );
            $content = trim($content);
        }

        return $cell
            ->addAttributes($column->bodyAttributes)
            ->content(PHP_EOL . $content . PHP_EOL)
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

    private function createUrl(ActionColumn $column, string $action, array|object $data, mixed $key): string
    {
        if ($column->urlCreator !== null) {
            return (string) ($column->urlCreator)($action, $data, $key);
        }

        $primaryKey = $column->primaryKey;
        $routeName = $column->routeName;

        if ($primaryKey !== '') {
            $key = $data[$primaryKey] ?? $key;
        }

        $currentRouteName = $this->currentRoute->getName() ?? '';

        $route = $routeName === null
            ? $currentRouteName . '/' . $action
            : $routeName . '/' . $action;


        $urlParamsConfig = array_merge(
            $column->urlParamsConfig,
            is_array($key) ? $key : [$primaryKey => $key]
        );

        if ($column->urlArguments !== null) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($column->urlArguments, $urlParamsConfig);
            $urlQueryParameters = [];
        } else {
            $urlArguments = [];
            $urlQueryParameters = array_merge($column->urlQueryParameters, $urlParamsConfig);
        }

        return $this->urlGenerator->generate($route, $urlArguments, $urlQueryParameters);
    }

    private function isVisibleButton(
        ActionColumn $column,
        string $name,
        array|object $data,
        mixed $key,
        int $index
    ): bool {
        $visibleButtons = $column->visibleButtons;

        if (empty($visibleButtons)) {
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
     * Initializes the default button rendering callback for single button.
     * @psalm-return array<string,Closure>
     */
    private function getDefaultButtons(): array
    {
        return [
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
