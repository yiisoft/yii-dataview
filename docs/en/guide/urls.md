# URLs

The Yii DataView component provides a flexible and customizable URL management system through the `UrlConfig` class. This system handles URL generation for pagination, sorting, and filtering in a consistent and configurable way.

## URL Configuration

### Basic Setup

The most basic URL configuration can be created with default settings:

```php
use Yiisoft\Yii\DataView\UrlConfig;

$config = new UrlConfig();

echo GridView::widget()
    ->urlConfig($config)
    ->dataReader($dataReader);
```

### Customizing Parameters

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

### Additional Parameters

You can add fixed arguments and query parameters:

```php
$config = (new UrlConfig())
    ->withArguments([
        'category' => 'books',
        'status' => 'active',
    ])
    ->withQueryParameters([
        'language' => 'en',
        'format' => 'html',
    ]);
```

## URL Parameter Types

The component supports two types of URL parameters defined in `UrlParameterType`:

1. **PATH Parameters** (`UrlParameterType::PATH`):
   ```
   /books/page/2/sort/name-desc
   ```

2. **QUERY Parameters** (`UrlParameterType::QUERY`):
   ```
   /books?page=2&sort=name-desc
   ```

Example configuration mixing both types:

```php
$config = (new UrlConfig())
    // Use path parameter for page
    ->withPageParameterType(UrlParameterType::PATH)
    // Use query parameter for sorting
    ->withSortParameterType(UrlParameterType::QUERY);

// Results in URLs like: /books/page/2?sort=name-desc
```

## URL Creation

### Basic URL Creator

You can provide a custom URL creator function:

```php
echo GridView::widget()
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
    });
```

### Yii Router Integration

For Yii applications, you can use the built-in router integration:

```php
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;

echo GridView::widget()
    ->urlParameterProvider(new UrlParameterProvider($currentRoute))
    ->urlCreator(new UrlCreator($urlGenerator));
```

## Parameter Handling

### Page Parameters

```php
$config = (new UrlConfig())
    ->withPageParameterName('page')      // Default: 'page'
    ->withPageParameterType(UrlParameterType::QUERY);

// Results in: ?page=2
```

### Keyset Pagination Parameters

```php
$config = (new UrlConfig())
    ->withPreviousPageParameterName('prev')  // Default: 'prev-page'
    ->withPreviousPageParameterType(UrlParameterType::QUERY);

// Results in: ?prev=token123
```

### Sort Parameters

```php
$config = (new UrlConfig())
    ->withSortParameterName('orderBy')    // Default: 'sort'
    ->withSortParameterType(UrlParameterType::QUERY);

// Results in: ?orderBy=name,-created_at
```

### Page Size Parameters

```php
$config = (new UrlConfig())
    ->withPageSizeParameterName('limit')   // Default: 'pageSize'
    ->withPageSizeParameterType(UrlParameterType::QUERY);

// Results in: ?limit=20
```

## Best Practices

1. **Parameter Naming**
   - Use consistent parameter names across your application
   - Choose clear, descriptive names for better maintainability
   - Consider URL length when choosing parameter names

2. **Parameter Types**
   - Use PATH parameters for required, structural elements
   - Use QUERY parameters for optional filters and settings
   - Consider SEO implications when choosing parameter types

3. **URL Structure**
   - Keep URLs clean and readable
   - Follow REST conventions when applicable
   - Consider caching implications of URL structure

4. **Security**
   - Validate all URL parameters before use
   - Escape output properly
   - Consider using signed URLs for sensitive operations

## Complete Example

Here's a complete example showing various URL configurations:

```php
use Yiisoft\Yii\DataView\UrlConfig;
use Yiisoft\Yii\DataView\UrlParameterType;
use Yiisoft\Yii\DataView\GridView;

$config = (new UrlConfig())
    // Configure page parameters
    ->withPageParameterName('p')
    ->withPageParameterType(UrlParameterType::PATH)
    ->withPreviousPageParameterName('prev')
    ->withPreviousPageParameterType(UrlParameterType::QUERY)
    
    // Configure sorting
    ->withSortParameterName('orderBy')
    ->withSortParameterType(UrlParameterType::QUERY)
    
    // Configure page size
    ->withPageSizeParameterName('limit')
    ->withPageSizeParameterType(UrlParameterType::QUERY)
    
    // Add fixed parameters
    ->withArguments(['category' => 'books'])
    ->withQueryParameters(['format' => 'html']);

echo GridView::widget()
    ->urlConfig($config)
    ->dataReader($dataReader)
    ->render();

// Example URLs generated:
// /books/p/1?orderBy=title&limit=20&format=html
// /books/p/2?prev=1&orderBy=-created&limit=20&format=html