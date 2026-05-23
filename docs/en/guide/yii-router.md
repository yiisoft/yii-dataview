# Yii Router Integration

The package provides a built-in integration with [Yii Router](https://github.com/yiisoft/router), so that URLs are
generated and read through the router instead of being built by hand.

It is wired through two widget methods, available on both `GridView` and `ListView`:

- `urlCreator()` - sets the callback that builds URLs for pagination, sorting, and filtering links.
- `urlParameterProvider()` - sets the provider that reads the current page, sort, and page size from the request.

The package ships ready-made implementations in the `Yiisoft\Yii\DataView\YiiRouter` namespace:

- [`UrlCreator`](#urlcreator) - builds URLs through the router; pass it to `urlCreator()`.
- [`UrlParameterProvider`](#urlparameterprovider) - reads current URL parameter values; pass it to `urlParameterProvider()`.

URLs for action buttons in `ActionColumn` are configured separately, through the column's `urlCreator` and
`urlConfig` parameters, using these classes. This part is `GridView`-specific:

- [`ActionColumnUrlCreator`](#actioncolumnurlcreator) - builds action button URLs for `ActionColumn`.
- [`ActionColumnUrlConfig`](#actioncolumnurlconfig) - per-column URL configuration for `ActionColumn`.

You can wire the integration either directly on a widget instance or globally through the widget configuration.

## Setting up manually

Pass the integration objects to the widget directly:

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlConfig;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

echo GridView::widget()
    ->dataReader($dataReader)
    ->urlParameterProvider(new UrlParameterProvider($currentRoute))
    ->urlCreator(new UrlCreator($urlGenerator))
    ->columns(
        new DataColumn(property: 'id'),
        new DataColumn(property: 'name'),
        new ActionColumn(
            urlCreator: new ActionColumnUrlCreator($urlGenerator, $currentRoute),
            urlConfig: new ActionColumnUrlConfig(baseRouteName: 'user'),
        ),
    );
```

## Setting up via configuration

Instead of wiring the integration on every widget instance, you can configure it once for all widgets
through the widget configuration (themes/params):

```php
use Yiisoft\Definitions\Reference;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

return [
    GridView::class => [
        'addColumnRendererConfigs()' => [
            [
                ActionColumnRenderer::class => [
                    'urlCreator' => Reference::to(ActionColumnUrlCreator::class),
                ],
            ],
        ],
        'urlParameterProvider()' => [
            Reference::to(UrlParameterProvider::class),
        ],
        'urlCreator()' => [
            Reference::to(UrlCreator::class),
        ],
    ],
];
```

For `ListView`, configure `ListView::class` the same way with `urlParameterProvider()` and `urlCreator()`.

## Classes

### UrlCreator

`UrlCreator` is a callable that generates URLs using `$urlGenerator->generateFromCurrent()`. It receives route
arguments and query parameters and returns a URL based on the current route with those parameters applied.

### UrlParameterProvider

`UrlParameterProvider` reads the current values of URL parameters (page, sort, page size) from the current
request. It retrieves path parameters from `CurrentRoute::getArgument()` and query parameters from `$_GET`.

### ActionColumnUrlCreator

`ActionColumnUrlCreator` generates URLs for action buttons (view, update, delete, etc.) in `ActionColumn`. It builds
route names by appending the action name to a base route name, e.g. `user/view`, `user/update`.

Constructor parameters:

- `UrlGeneratorInterface $urlGenerator` - The URL generator service.
- `CurrentRoute $currentRoute` - The current route service.
- `string $defaultPrimaryKey` - Default primary key field name. Default: `'id'`.
- `UrlParameterType $defaultPrimaryKeyParameterType` - Default parameter type for the primary key value.
  Default: `UrlParameterType::Query`.

When invoked, `ActionColumnUrlCreator`:

1. Reads the primary key value from the row data (using the configured primary key field name).
2. Builds the route name as `{baseRouteName}/{action}`. If no base route name is set, uses the current route name.
3. Adds the primary key value to either path arguments or query parameters, depending on the parameter type.
4. Calls `$urlGenerator->generate()` with the route, arguments, and query parameters.

### ActionColumnUrlConfig

`ActionColumnUrlConfig` allows per-column URL configuration. Pass it to `ActionColumn` via the `urlConfig` parameter:

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlConfig;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

new ActionColumn(
    urlCreator: new ActionColumnUrlCreator($urlGenerator, $currentRoute),
    urlConfig: new ActionColumnUrlConfig(
        primaryKey: 'user_id',
        baseRouteName: 'admin/user',
        arguments: ['section' => 'users'],
        queryParameters: ['ref' => 'grid'],
        primaryKeyParameterType: UrlParameterType::Path,
    ),
);
```

Constructor parameters (all optional):

- `?string $primaryKey` - The field name of the primary key in the data. When `null`, falls back to the
  `$defaultPrimaryKey` from `ActionColumnUrlCreator` (which defaults to `'id'`).
- `?string $baseRouteName` - The base route name. The action name is appended to it (e.g. `'admin/user'` becomes
  `'admin/user/view'`). When `null`, the current route name is used.
- `array $arguments` - Additional route arguments to include in the generated URL. Default: `[]`.
- `array $queryParameters` - Additional query parameters to append to the URL. Default: `[]`.
- `?UrlParameterType $primaryKeyParameterType` - How to pass the primary key in the URL. `UrlParameterType::Path`
  puts it in route arguments (e.g. `/user/view/42`), `UrlParameterType::Query` puts it in the query string
  (e.g. `/user/view?id=42`). When `null`, falls back to `$defaultPrimaryKeyParameterType` from
  `ActionColumnUrlCreator` (which defaults to `UrlParameterType::Query`).
