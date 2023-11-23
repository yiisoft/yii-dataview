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
        private UrlGeneratorInterface $urlGenerator,
        private CurrentRoute $currentRoute,
    ) {
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        // TODO: Implement renderColumn() method.
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        $this->checkColumn($column);
        return $cell->content($column->getHeader() ?? 'Actions');
    }

    public function renderFilter(ColumnInterface $column, Cell $cell, GlobalContext $context): ?Cell
    {
        return null;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        $this->checkColumn($column);

        $buttons = empty($column->getButtons()) ? $this->getDefaultButtons() : $column->getButtons();

        $content = preg_replace_callback(
            '/{([\w\-\/]+)}/',
            function (array $matches) use ($column, $buttons, $context): string {
                $name = $matches[1];

                if (
                    $this->isVisibleButton(
                        $column,
                        $name,
                        $context->getData(),
                        $context->getKey(),
                        $context->getIndex()
                    ) &&
                    isset($buttons[$name]) &&
                    $buttons[$name] instanceof Closure
                ) {
                    $url = $this->createUrl($column, $name, $context->getData(), $context->getKey());
                    return (string)$buttons[$name]($url);
                }

                return '';
            },
            $column->getTemplate()
        );

        return $cell
            ->addAttributes($column->getBodyAttributes())
            ->content(PHP_EOL . $content . PHP_EOL)
            ->encode(false);
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        // TODO: Implement renderFooter() method.
    }

    private function createUrl(ActionColumn $column, string $action, array|object $data, mixed $key): string
    {
        $primaryKey = $column->getPrimaryKey();
        $routeName = $column->getRouteName();

        if ($primaryKey !== '') {
            $key = $data[$primaryKey] ?? $key;
        }

        $currentRouteName = $this->currentRoute->getName() ?? '';

        $route = $routeName === null
            ? $currentRouteName . '/' . $action
            : $routeName . '/' . $action;


        $urlParamsConfig = array_merge(
            $column->getUrlParamsConfig(),
            is_array($key) ? $key : [$primaryKey => $key]
        );

        if ($column->getUrlArguments() !== null) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($column->getUrlArguments(), $urlParamsConfig);
            $urlQueryParameters = [];
        } else {
            $urlArguments = [];
            $urlQueryParameters = array_merge($column->getUrlQueryParameters(), $urlParamsConfig);
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
        $visibleButtons = $column->getVisibleButtons();

        if (empty($visibleButtons)) {
            return true;
        }

        $visibleValue = $visibleButtons[$name] ?? false;
        if (is_bool($visibleValue)) {
            return $visibleValue;
        }

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
     * @psalm-assert self $column
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
