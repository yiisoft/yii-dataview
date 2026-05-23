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

## Integrations

The examples above build URLs manually. In a real application you usually delegate URL generation and parameter
reading to your framework's router by providing a `urlCreator` callback and a `urlParameterProvider`.

For Yii applications, the package ships with a built-in [Yii Router integration](yii-router.md).
