# URLs

The Yii DataView component provides a flexible and customizable URL management system through the `UrlConfig` class.
This system handles URL generation for pagination, sorting, and filtering in a consistent and configurable way.

The most basic URL configuration can be created with default settings:

```php
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\GridView;

$config = new UrlConfig();

echo GridView::widget()
    ->urlConfig($config)
    ->dataReader($dataReader);
```

## Parameter names and types

You can customize parameter names and types:

```php
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\UrlParameterType;

$config = (new UrlConfig())
    // Page parameters
    ->withPageParameterName('p')
    ->withPageParameterType(UrlParameterType::PATH)
    ->withPreviousPageParameterName('prev')
    ->withPreviousPageParameterType(UrlParameterType::QUERY)
    
    // Sorting parameters
    ->withSortParameterName('orderBy')
    ->withSortParameterType(UrlParameterType::QUERY)
    
    // Page size parameters
    ->withPageSizeParameterName('limit')
    ->withPageSizeParameterType(UrlParameterType::QUERY);
```

If needed, you can add fixed arguments and query parameters to the URL generated:

```php
use Yiisoft\Yii\DataView\UrlConfig;

$config = (new UrlConfig())
    // /books/page/2/active
    ->withArguments([
        'category' => 'books',
        'status' => 'active',
    ])
    // /books/page/2?language=en&format=html
    ->withQueryParameters([
        'language' => 'en',
        'format' => 'html',
    ]);
```

Page and sorting parameters could be included either in the path or in the query:

```php
use Yiisoft\Yii\DataView\UrlConfig;

$config = (new UrlConfig())
    // Use path parameter for page
    ->withPageParameterType(UrlParameterType::PATH)
    // Use query parameter for sorting
    ->withSortParameterType(UrlParameterType::QUERY);
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
