# URLs

You can adjust URLs used in grids and lists for pagination, sorting, and filtering.

## Parameter names and types

You can customize parameter names and types:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\Url\UrlParameterType;
?>

<?= GridView::widget()
    // Page parameters
    ->pageParameterName('p') // Name of the parameter for the current page number. Default is `page`.
    ->pageParameterType(UrlParameterType::Path) // Type of the page parameter. Default is `UrlParameterType::Query`.
    ->previousPageParameterName('prev') // Name of the parameter for the previous page. Default is `prev-page`.
    ->previousPageParameterType(UrlParameterType::Path) // Type of the previous page parameter. Default is `UrlParameterType::Query`.

    // Sorting parameter
    ->sortParameterName('orderBy') // Name of the parameter for sorting configuration. Default is `sort`.
    ->sortParameterType(UrlParameterType::Query) // Type of the sort parameter. Default is `UrlParameterType::Query`.

    // Page size parameters
    ->pageSizeParameterName('limit') // Name of the parameter for items per page. Default is `pagesize`.
    ->pageSizeParameterType(UrlParameterType::Query); // Type of the page size parameter. Default is `UrlParameterType::Query`.
?>
```

`UrlParameterType` is an enum with two cases:

- `UrlParameterType::Path` - the parameter is part of the URL path, e.g. `/page/2/sort/name-desc`.
- `UrlParameterType::Query` - the parameter is in the query string, e.g. `?page=2&sort=name-desc`.

If needed, you can add fixed arguments and query parameters to the URL generated:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    // /books/page/2/active
    ->urlArguments([
        'category' => 'books',
        'status' => 'active',
    ])
    // /books/page/2?language=en&format=html
    ->urlQueryParameters([
        'language' => 'en',
        'format' => 'html',
    ])
?>
```

A custom callback could be used instead of configuration:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->urlCreator(function (array $arguments, array $queryParameters): string {
        $baseUrl = '/books';
        $pathParams = [];

        // Handle path parameters
        foreach ($arguments as $name => $value) {
            $pathParams[] = "$name-$value";
        }

        // Build final URL
        $url = $baseUrl;
        if ($pathParams) {
            $url .= '/' . implode('/', $pathParams);
        }
        if ($queryParameters) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    })
?>
```

## Yii Router Integration

For Yii applications, you can use the built-in router integration:

```php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

echo GridView::widget()
    ->urlParameterProvider(new UrlParameterProvider($currentRoute))
    ->urlCreator(new UrlCreator($urlGenerator));
```

`UrlCreator` is a callable that generates URLs using `$urlGenerator->generateFromCurrent()`. It receives route
arguments and query parameters and returns a URL based on the current route with those parameters applied.

`UrlParameterProvider` reads the current values of URL parameters (page, sort, page size) from the current
request. It retrieves path parameters from `CurrentRoute::getArgument()` and query parameters from `$_GET`.

### ActionColumnUrlCreator

`ActionColumnUrlCreator` generates URLs for action buttons (view, edit, delete, etc.) in `ActionColumn`. It builds
route names by appending the action name to a base route name, e.g. `user/view`, `user/edit`.

Constructor parameters:

- `UrlGeneratorInterface $urlGenerator` - The URL generator service.
- `CurrentRoute $currentRoute` - The current route service.
- `string $defaultPrimaryKey` - Default primary key field name. Default: `'id'`.
- `UrlParameterType $defaultPrimaryKeyParameterType` - Default parameter type for the primary key value.
  Default: `UrlParameterType::Query`.

Usage:

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;

// The ActionColumnUrlCreator is a callable with signature:
// function(string $action, DataContext $context): string

$gridView = GridView::widget()
    ->dataReader($paginator)
    ->columns(
        new ActionColumn(
            urlCreator: new ActionColumnUrlCreator($urlGenerator, $currentRoute),
        ),
    );
```

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
