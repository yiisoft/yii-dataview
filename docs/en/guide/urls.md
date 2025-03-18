# URLs

You can adjust URLs used in grids and lists for pagination, sorting, and filtering.

## Parameter names and types

You can customize parameter names and types:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\UrlParameterType;
?>

<?= GridView::widget()
    // Page parameters
    ->pageParameterName('p') // Name of the parameter for the current page number. Default is `page`.
    ->pageParameterType(UrlParameterType::PATH) // Type of the page parameter. Default is `UrlParameterType::QUERY`.
    ->previousPageParameterName('prev') // Name of the parameter for the previous page. Default is `prev-page`.
    ->previousPageParameterType(UrlParameterType::PATH) // Type of the previous page parameter. Default is `UrlParameterType::QUERY`.
    
    // Sorting parameter
    ->sortParameterName('orderBy') // Name of the parameter for sorting configuration. Default is `sort`.
    ->sortParameterType(UrlParameterType::QUERY) // Type of the sort parameter. Default is `UrlParameterType::QUERY`.
    
    // Page size parameters
    ->pageSizeParameterName('limit') // Name of the parameter for items per page. Default is `pagesize`.
    ->pageSizeParameterType(UrlParameterType::QUERY); // Type of the page size parameter. Default is `UrlParameterType::QUERY`.
?>
```

If needed, you can add fixed arguments and query parameters to the URL generated:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\UrlParameterType;
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
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\GridView;
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
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

echo GridView::widget()
    ->urlParameterProvider(new UrlParameterProvider($currentRoute))
    ->urlCreator(new UrlCreator($urlGenerator));
```
