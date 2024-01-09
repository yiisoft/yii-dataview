<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use LogicException;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

use function is_object;

final class ActionColumnUrlCreator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
        private readonly string $defaultPrimaryKey = 'id',
        private readonly bool $defaultPrimaryKeyPlace = UrlConfig::QUERY_PARAMETERS,
    ) {
    }

    public function __invoke(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        $config = $column->urlConfig ?? new UrlConfig();
        if (!$config instanceof UrlConfig) {
            throw new LogicException(self::class . ' supports ' . UrlConfig::class . ' only.');
        }

        $primaryKey = $config->primaryKey ?? $this->defaultPrimaryKey;
        $primaryKeyPlace = $config->primaryKeyPlace ?? $this->defaultPrimaryKeyPlace;

        $primaryKeyValue = is_object($context->data)
            ? $context->data->$primaryKey
            : $context->data[$primaryKey];

        /** @psalm-suppress PossiblyNullOperand We guess that current route is matched. */
        $route = ($config->baseRouteName ?? $this->currentRoute->getName()) . '/' . $action;

        $arguments = $config->arguments;
        $queryParameters = $config->queryParameters;
        if ($primaryKeyPlace === UrlConfig::ARGUMENTS) {
            $arguments = array_merge($arguments, [$primaryKey => (string)$primaryKeyValue]);
        } else {
            $queryParameters = array_merge($queryParameters, [$primaryKey => (string)$primaryKeyValue]);
        }

        return $this->urlGenerator->generate($route, $arguments, $queryParameters);
    }
}
