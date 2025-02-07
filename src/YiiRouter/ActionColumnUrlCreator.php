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

/**
 * URL creator for action columns in GridView.
 *
 * This class is responsible for generating URLs for action buttons (like view, edit,
 * delete) in GridView action columns. It supports:
 * - Automatic primary key handling
 * - Flexible URL parameter configuration
 * - Path and query parameter styles
 * - Default configuration with overrides
 *
 * Example usage:
 * ```php
 * // Create the URL creator
 * $urlCreator = new ActionColumnUrlCreator(
 *     urlGenerator: $container->get(UrlGeneratorInterface::class),
 *     currentRoute: $container->get(CurrentRoute::class),
 *     defaultPrimaryKey: 'id',
 *     defaultPrimaryKeyParameterType: UrlParameterType::QUERY
 * );
 *
 * // Use in GridView action column
 * $grid->column(
 *     ActionColumn::class,
 *     urlCreator: $urlCreator,
 *     urlConfig: new ActionColumnUrlConfig(
 *         primaryKey: 'uuid',
 *         baseRouteName: 'user',
 *         primaryKeyParameterType: UrlParameterType::PATH
 *     )
 * )
 * ```
 *
 * The above configuration will generate URLs like:
 * - View: /user/view/123
 * - Edit: /user/edit/123
 * - Delete: /user/delete/123
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
     * @param int $defaultPrimaryKeyParameterType The default parameter type for
     * primary key values. Used when not specified in the URL config.
     * @psalm-param UrlParameterType::* $defaultPrimaryKeyParameterType
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CurrentRoute $currentRoute,
        private readonly string $defaultPrimaryKey = 'id',
        private readonly int $defaultPrimaryKeyParameterType = UrlParameterType::QUERY,
    ) {
    }

    /**
     * Generates a URL for an action button.
     *
     * This method:
     * 1. Extracts the primary key value from the data row
     * 2. Determines the base route name (from config or current route)
     * 3. Appends the action name to the route
     * 4. Adds the primary key value as either a path or query parameter
     * 5. Generates the final URL with all parameters
     *
     * @param string $action The action name (e.g., 'view', 'edit', 'delete').
     * @param DataContext $context The data context containing the row data and column.
     *
     * @throws LogicException if the URL config is not an instance of ActionColumnUrlConfig.
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
