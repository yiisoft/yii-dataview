<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use LogicException;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\UrlParameterType;

use function is_object;

final class ActionColumnUrlCreator
{
    /**
     * @psalm-param UrlParameterType::* $defaultPrimaryKeyParameterType
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
        private readonly string $defaultPrimaryKey = 'id',
        private readonly int $defaultPrimaryKeyParameterType = UrlParameterType::QUERY,
    ) {
    }

    public function __invoke(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $config = $column->urlConfig ?? new ActionColumnUrlConfig();
        if (!$config instanceof ActionColumnUrlConfig) {
            throw new LogicException(self::class . ' supports ' . ActionColumnUrlConfig::class . ' only.');
        }

        $primaryKey = $config->primaryKey ?? $this->defaultPrimaryKey;
        $primaryKeyParameterType = $config->primaryKeyParameterType ?? $this->defaultPrimaryKeyParameterType;

        $primaryKeyValue = is_object($context->data)
            ? $context->data->$primaryKey
            : $context->data[$primaryKey];

        /** @psalm-suppress PossiblyNullOperand Assume that the current route matches. */
        $route = ($config->baseRouteName ?? $this->currentRoute->getName()) . '/' . $action;

        $arguments = $config->arguments;
        $queryParameters = $config->queryParameters;
        switch ($primaryKeyParameterType) {
            case UrlParameterType::PATH:
                $arguments = array_merge($arguments, [$primaryKey => (string) $primaryKeyValue]);
                break;
            case UrlParameterType::QUERY:
                $queryParameters = array_merge($queryParameters, [$primaryKey => (string) $primaryKeyValue]);
                break;
        }

        return $this->urlGenerator->generate($route, $arguments, $queryParameters);
    }
}
