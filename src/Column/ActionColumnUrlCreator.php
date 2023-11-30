<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

use function is_object;

final class ActionColumnUrlCreator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
        private readonly ?string $defaultPrimaryKey = 'id',
    ) {
    }

    public function __invoke(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $primaryKey = $column->primaryKey ?? $this->defaultPrimaryKey;
        $routeName = $column->routeName;

        if (!empty($primaryKey)) {
            $key = (is_object($context->data) ? $context->data->$primaryKey : $context->data[$primaryKey])
                ?? $context->key;
        } else {
            $key = $context->key;
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
}
