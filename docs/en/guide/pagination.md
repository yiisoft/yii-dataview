# Pagination and page sizes

The [yiisoft/data](https://github.com/yiisoft/data), which is the source of data for data view widgets, provides two types of pagination:
offset-based and keyset-based. There are two corresponding widgets for rendering these: `OffsetPagination` and
`KeysetPagination`.

## Offset Pagination

[Offset pagination](https://github.com/yiisoft/data#offset-pagination) is the traditional pagination method that
uses page numbers and is the best suitable for not that many pages and not that many data changes.

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($paginator)
    ->offsetPaginationConfig([
        'listTag()' => ['ul'],
        'listAttributes()' => [['class' => 'pagination']],
        'itemTag()' => ['li'],
        'itemAttributes()' => [['class' => 'page-item']],
        'linkAttributes()' => [['class' => 'page-link']],
        'currentItemClass()' => ['active'],
        'disabledItemClass()' => ['disabled'],
    ])
?>
```

See `OffsetPagination` class methods for possible configuration options. 

## Keyset Pagination

[Keyset pagination](https://github.com/yiisoft/data#keyset-pagination) (also known as cursor pagination) uses
unique keys or tokens to navigate through data. It's more efficient for large datasets and provides consistent results
even when data changes between page loads.

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($paginator)
    ->keysetPaginationConfig([
        'listTag()' => ['ul'],
        'listAttributes()' => [['class' => 'pagination']],
        'itemTag()' => ['li'],
        'itemAttributes()' => [['class' => 'page-item']],
        'linkAttributes()' => [['class' => 'page-link']],
        'disabledItemClass()' => ['disabled'],
    ])
?>
```

See `KeysetPagination` class methods for possible configuration options. 

## Page sizes

By default, page size is fixed, but you can configure it to be dynamic:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
?>

<?= GridView::widget()
    ->dataReader($paginator)
    // Configure page size
    ->pageSizeConstraint([10, 20, 50, 100])
    ->pageSizeWidget(
        SelectPageSize::widget()
            ->addAttributes([
                'class' => 'form-select',
                'aria-label' => 'Items per page'
            ])
    )
    // Configure wrapper
    ->pageSizeTag('div')
    ->pageSizeAttributes([
        'class' => 'page-size-wrapper',
        'id' => 'page-size-control'
    ])
    // Configure template
    ->pageSizeTemplate(<<<HTML
        <label for="page-size-control">
            Show {widget} items per page
        </label>
    HTML)
?>
```
