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

### OffsetPagination options

All configuration methods return a new immutable instance.

**Visibility:**

- `showOnSinglePage(bool $show = true)` - Show pagination even when all data fits on one page. Default: `false`
  (pagination is hidden when only one page exists).

**Labels:**

- `labelFirst(string|Stringable|null $label)` - Label for the "first page" link. Default: `'⟪'`. Pass `null` to hide
  this link.
- `labelLast(string|Stringable|null $label)` - Label for the "last page" link. Default: `'⟫'`. Pass `null` to hide
  this link.
- `labelPrevious(string|Stringable|null $label)` - Label for the "previous page" link. Default: `'⟨'`. Pass `null`
  to hide this link.
- `labelNext(string|Stringable|null $label)` - Label for the "next page" link. Default: `'⟩'`. Pass `null` to hide
  this link.

**Navigation:**

- `maxNavLinkCount(int $value)` - Maximum number of page number links to show. Default: `10`. When there are more pages
  than this limit, a sliding window of links is shown around the current page.

**CSS classes for current/disabled states:**

- `currentItemClass(?string $class)` - CSS class added to the item tag (`itemTag`) of the current page.
- `disabledItemClass(?string $class)` - CSS class added to the item tag of disabled links (first/previous on page 1,
  next/last on the last page).
- `currentLinkClass(?string $class)` - CSS class added to the `<a>` tag of the current page link.
- `disabledLinkClass(?string $class)` - CSS class added to the `<a>` tag of disabled links.

**Link attributes:**

- `linkAttributes(array $attributes)` - Set HTML attributes for all `<a>` link elements (replaces existing attributes).
- `addLinkAttributes(array $attributes)` - Merge additional HTML attributes into existing link attributes.
- `linkClass(BackedEnum|string|null ...$class)` - Set CSS classes on link elements (replaces existing classes).
- `addLinkClass(BackedEnum|string|null ...$class)` - Add CSS classes to link elements without removing existing ones.

**HTML structure:**

The pagination markup is structured as container > list > item > link. Each level can be customized or removed.

- `containerTag(?string $tag)` - Outer container tag. Default: `'nav'`. Pass `null` to remove the container.
- `containerAttributes(array $attributes)` - HTML attributes for the container tag.
- `listTag(?string $tag)` - Tag wrapping all pagination items. Default: `null` (no list wrapper). Common value: `'ul'`.
- `listAttributes(array $attributes)` - HTML attributes for the list tag.
- `itemTag(?string $tag)` - Tag wrapping each individual link. Default: `null` (no item wrapper). Common value: `'li'`.
- `itemAttributes(array $attributes)` - HTML attributes for item tags.

**Example with Bootstrap 5 styling:**

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($paginator)
    ->offsetPaginationConfig([
        'containerTag()' => ['nav'],
        'containerAttributes()' => [['aria-label' => 'Page navigation']],
        'listTag()' => ['ul'],
        'listAttributes()' => [['class' => 'pagination']],
        'itemTag()' => ['li'],
        'itemAttributes()' => [['class' => 'page-item']],
        'linkAttributes()' => [['class' => 'page-link']],
        'currentItemClass()' => ['active'],
        'disabledItemClass()' => ['disabled'],
        'labelFirst()' => ['First'],
        'labelLast()' => ['Last'],
        'labelPrevious()' => ['Previous'],
        'labelNext()' => ['Next'],
        'maxNavLinkCount()' => [5],
    ])
?>
```

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

### KeysetPagination options

Keyset pagination only shows "previous" and "next" links (no page numbers). All configuration methods return a new
immutable instance.

**Visibility:**

- `showOnSinglePage(bool $show = true)` - Show pagination even when all data fits on one page. Default: `false`.

**Labels:**

- `labelPrevious(string|Stringable $label)` - Label for the "previous page" link. Default: `'⟨'`.
- `labelNext(string|Stringable $label)` - Label for the "next page" link. Default: `'⟩'`.

**CSS classes for disabled state:**

- `disabledItemClass(?string $class)` - CSS class added to the item tag of disabled links (previous on the first page,
  next on the last page).
- `disabledLinkClass(?string $class)` - CSS class added to the `<a>` tag of disabled links.

**Link attributes:**

- `linkAttributes(array $attributes)` - Set HTML attributes for all `<a>` link elements (replaces existing attributes).
- `linkClass(BackedEnum|string|null ...$class)` - Set CSS classes on link elements (replaces existing classes).
- `addLinkClass(BackedEnum|string|null ...$class)` - Add CSS classes to link elements without removing existing ones.

**HTML structure:**

- `containerTag(?string $tag)` - Outer container tag. Default: `'nav'`. Pass `null` to remove the container.
- `containerAttributes(array $attributes)` - HTML attributes for the container tag.
- `listTag(?string $tag)` - Tag wrapping all pagination items. Default: `null`. Common value: `'ul'`.
- `listAttributes(array $attributes)` - HTML attributes for the list tag.
- `itemTag(?string $tag)` - Tag wrapping each link. Default: `null`. Common value: `'li'`.
- `itemAttributes(array $attributes)` - HTML attributes for item tags.

## Page sizes

By default, page size is fixed, but you can configure it to be dynamic.

### Page size constraint

The `pageSizeConstraint()` method on GridView/ListView controls what page sizes users are allowed to pick. It accepts
several types of values:

- **`true`** (default) — The page size is fixed at the default value. Users cannot change it. The page size widget
  is not rendered.
- **`false`** — Any page size value is allowed. Useful with `InputPageSize` where the user types a number.
- **`int`** — Sets a maximum page size. Users can pick any value from 1 up to this number. Useful with `InputPageSize`.
- **`array`** (list of integers) — A specific list of page sizes to choose from, e.g. `[10, 20, 50, 100]`. Works
  with `SelectPageSize` to show a dropdown. The `SelectPageSize` widget will only render when the array contains
  at least 2 values.

### SelectPageSize widget

`SelectPageSize` renders a `<select>` dropdown for choosing a page size from a predefined list. Pair it with an
array constraint:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;
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

`SelectPageSize` methods:

- `attributes(array $attributes)` - Replace all HTML attributes on the `<select>` element.
- `addAttributes(array $attributes)` - Merge additional attributes into existing ones.

### InputPageSize widget

`InputPageSize` renders a text `<input>` for entering a page size value. Use it when you want the user to type a
number instead of picking from a list. It works well with an `int` (maximum) or `false` (any value) constraint:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
?>

<?= GridView::widget()
    ->dataReader($paginator)
    // Allow any page size up to 200
    ->pageSizeConstraint(200)
    ->pageSizeWidget(
        InputPageSize::widget()
            ->addAttributes([
                'class' => 'form-control',
                'type' => 'number',
                'min' => '1',
                'max' => '200',
                'style' => 'width: 80px',
            ])
    )
    ->pageSizeTemplate('Show {widget} items per page')
?>
```

`InputPageSize` methods:

- `attributes(array $attributes)` - Replace all HTML attributes on the `<input>` element.
- `addAttributes(array $attributes)` - Merge additional attributes into existing ones.

Both `SelectPageSize` and `InputPageSize` use JavaScript `onchange` handlers to navigate to the new URL when
the value changes. The URL is built from a pattern provided by the framework through `PageSizeContext`.

### Page size wrapper options

These methods are on `GridView` / `ListView` (via `BaseListView`):

- `pageSizeWidget(?PageSizeWidgetInterface $widget)` - Set the page size widget instance. Pass `null` to use
  the default `SelectPageSize`.
- `pageSizeTag(?string $tag)` - Wrapper tag around the page size control. Default: `'div'`. Pass `null` to remove
  the wrapper.
- `pageSizeAttributes(array $attributes)` - HTML attributes for the wrapper tag.
- `pageSizeTemplate(?string $template)` - Template string. Use `{widget}` as a placeholder for the page size widget.
  Default: `'Results per page {widget}'`. Pass `null` or an empty string to hide the control entirely.
