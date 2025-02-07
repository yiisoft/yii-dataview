# Pagination

The Yii DataView component provides two types of pagination: offset-based and keyset-based. Each type has its own advantages and use cases.

## Overview

Pagination in Yii DataView is implemented through dedicated widgets that work with corresponding paginators:

- `OffsetPagination` widget with `OffsetPaginator`
- `KeysetPagination` widget with `KeysetPaginator`

Both widgets implement the `PaginationWidgetInterface` and provide a fluent interface for configuration.

## Offset Pagination

Offset pagination is the traditional pagination method that uses page numbers. It's suitable for relatively static data where the total number of items is known and doesn't change frequently.

### Basic Usage

```php
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

echo OffsetPagination::widget()
    ->withPaginator($offsetPaginator)
    ->withContext($paginationContext);
```

### Customization

The widget is highly customizable:

```php
echo OffsetPagination::widget()
    // HTML structure
    ->containerTag('nav')
    ->listTag('ul')
    ->itemTag('li')
    
    // HTML attributes
    ->containerAttributes(['aria-label' => 'Page navigation'])
    ->listAttributes(['class' => 'pagination'])
    ->itemAttributes(['class' => 'page-item'])
    ->linkAttributes(['class' => 'page-link'])
    
    // Styling classes
    ->currentItemClass('active')
    ->disabledItemClass('disabled')
    ->currentLinkClass('current')
    ->disabledLinkClass('disabled')
    
    // Navigation labels
    ->labelPrevious('Previous')
    ->labelNext('Next')
    ->labelFirst('First')
    ->labelLast('Last')
    
    // Maximum number of navigation links
    ->maxNavLinkCount(5)
    
    // Set paginator and context
    ->withPaginator($offsetPaginator)
    ->withContext($paginationContext);
```

This will generate HTML like:

```html
<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item disabled">
            <a class="page-link disabled" href="#">First</a>
        </li>
        <li class="page-item disabled">
            <a class="page-link disabled" href="#">Previous</a>
        </li>
        <li class="page-item active">
            <a class="page-link current" href="#">1</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="/items?page=2">2</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="/items?page=3">3</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="/items?page=2">Next</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="/items?page=10">Last</a>
        </li>
    </ul>
</nav>
```

## Keyset Pagination

Keyset pagination (also known as cursor pagination) uses unique keys or tokens to navigate through data. It's more efficient for large datasets and provides consistent results even when data changes between page loads.

### Basic Usage

```php
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;

echo KeysetPagination::widget()
    ->withPaginator($keysetPaginator)
    ->withContext($paginationContext);
```

### Customization

The widget supports similar customization options:

```php
echo KeysetPagination::widget()
    // HTML structure
    ->containerTag('nav')
    ->listTag('ul')
    ->itemTag('li')
    
    // HTML attributes
    ->containerAttributes(['aria-label' => 'Navigation'])
    ->listAttributes(['class' => 'pagination'])
    ->itemAttributes(['class' => 'page-item'])
    ->linkAttributes(['class' => 'page-link'])
    
    // Styling classes
    ->disabledItemClass('disabled')
    ->disabledLinkClass('disabled')
    
    // Navigation labels
    ->labelPrevious('Previous')
    ->labelNext('Next')
    
    // Set paginator and context
    ->withPaginator($keysetPaginator)
    ->withContext($paginationContext);
```

This will generate HTML like:

```html
<nav aria-label="Navigation">
    <ul class="pagination">
        <li class="page-item">
            <a class="page-link" href="/items?prev=token123">Previous</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="/items?next=token456">Next</a>
        </li>
    </ul>
</nav>
```

### When to Use Keyset Pagination

Keyset pagination is recommended when:
- Working with large datasets where offset-based pagination becomes slow
- Dealing with frequently changing data where page contents might shift
- Building infinite scroll or "Load More" interfaces
- Performance and consistency are critical

## Integration with GridView

Both pagination widgets can be used with GridView:

```php
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
// or use KeysetPagination

echo GridView::widget()
    ->dataReader($paginator)
    ->paginationWidget(
        OffsetPagination::widget()
            ->containerAttributes(['class' => 'my-pagination'])
    )
    ->render();
```

## URL Configuration

Pagination URLs can be customized using the `UrlConfig` class:

```php
use Yiisoft\Yii\DataView\UrlConfig;

$urlConfig = (new UrlConfig())
    // Configure page parameters
    ->withPageParameterName('p')
    ->withPageParameterType(UrlParameterType::PATH)
    ->withPreviousPageParameterName('prev')
    ->withPreviousPageParameterType(UrlParameterType::QUERY)
    
    // Configure page size
    ->withPageSizeParameterName('limit')
    ->withPageSizeParameterType(UrlParameterType::QUERY)
    
    // Add additional parameters
    ->withArguments(['category' => 'books'])
    ->withQueryParameters(['filter' => 'active']);

// Use with GridView
echo GridView::widget()
    ->dataReader($paginator)
    ->urlConfig($urlConfig)
    ->render();
```

This configuration would generate URLs like:
- `/books/p/2?limit=20&filter=active` (for offset pagination)
- `/books?prev=token123&limit=20&filter=active` (for keyset pagination)

