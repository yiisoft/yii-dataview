<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use LogicException;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

use function is_object;

/**
 * URL creator for action columns in `GridView`.
 */
final class ActionColumnUrlCreator
{
    /**
     * Creates a new URL creator instance.
     *
     * @param UrlGeneratorInterface $urlGenerator The URL generator service.
     * @param CurrentRoute $currentRoute The current route service.
     * @param string $defaultPrimaryKey The default primary key field name.
     * Used when not specified in the URL config.
     * @param UrlParameterType $defaultPrimaryKeyParameterType The default parameter type for primary key values.
     * Used when not specified in the URL config.
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
        private readonly string $defaultPrimaryKey = 'id',
        private readonly UrlParameterType $defaultPrimaryKeyParameterType = UrlParameterType::Query,
    ) {}

    /**
     * Generates a URL for an action button.
     *
     * @param string $action The action name (e.g., 'view', 'edit', 'delete').
     * @param DataContext $context The data context containing the row data and column.
     *
     * @throws LogicException if the URL config is not an instance of `ActionColumnUrlConfig`.
     *
     * @return string The generated URL for the action.
     */
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
            case UrlParameterType::Path:
                $arguments = array_merge($arguments, [$primaryKey => (string) $primaryKeyValue]);
                break;
            case UrlParameterType::Query:
                $queryParameters = array_merge($queryParameters, [$primaryKey => (string) $primaryKeyValue]);
                break;
        }

        return $this->urlGenerator->generate($route, $arguments, $queryParameters);
    }
}
