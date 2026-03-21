# Grid View

GridView displays data in a table with support for:

- Pagination
- Sorting (single and multi-column)
- Filtering
- Custom column rendering
- Action buttons
- Row customization
- Header and footer sections
- Column grouping
- Container and layout customization

## Basic Usage

```php
<?php
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;

/**
 * @var ReadableDataInterface $dataReader
 */
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    ->columns(
        new DataColumn(property: 'id'),
        new DataColumn(property: 'title', header: 'Post Title'),
        new DataColumn(property: 'created_at'),
    )
?>
```

The widget gets data from a data reader and renders it according to the column configuration passed to `columns()`.

## Column Types

GridView has several built-in column types.

### DataColumn

`DataColumn` is the default column type. It displays data values and supports sorting, filtering, and custom content.

**Constructor parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `property` | `?string` | `null` | Property name in the data model |
| `header` | `?string` | `null` | Header cell content. If `null`, the property name (ucfirst) is used |
| `encodeHeader` | `bool` | `true` | Whether to HTML-encode the header |
| `footer` | `?string` | `null` | Footer cell content |
| `columnAttributes` | `array` | `[]` | HTML attributes for all column cells |
| `headerAttributes` | `array` | `[]` | HTML attributes for the header cell |
| `bodyAttributes` | `array\|callable` | `[]` | HTML attributes for body cells. Can be a callable: `fn(array\|object $data, DataContext $context): array` |
| `withSorting` | `bool` | `true` | Whether this column is sortable |
| `content` | `mixed` | `null` | Custom cell content: string, callable, or `ValuePresenterInterface` |
| `encodeContent` | `?bool` | `null` | Whether to HTML-encode cell content (`null` = auto) |
| `filter` | `bool\|array\|FilterWidget` | `false` | Filter configuration |
| `filterFactory` | `string\|FilterFactoryInterface\|null` | `null` | Factory for creating data filters |
| `filterValidation` | `array\|RuleInterface\|null` | `null` | Validation rules for filter values |
| `filterEmpty` | `bool\|callable\|null` | `null` | Condition for empty filter value |
| `visible` | `bool` | `true` | Whether the column is visible |
| `columnClass` | `?string` | `null` | CSS class for all column cells |
| `headerClass` | `?string` | `null` | CSS class for the header cell |
| `bodyClass` | `string\|array\|callable\|null` | `null` | CSS class for body cells. Can be a callable: `fn(array\|object $data, DataContext $context): string\|array\|null` |

Basic example:

```php
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

new DataColumn(
    property: 'title',
    header: 'Post Title',
)
```

#### Custom content with a callable

```php
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

new DataColumn(
    property: 'name',
    content: static fn(object $data, DataContext $context): string => Html::encode($data->getFullName()),
)
```

#### Custom content with a value presenter

You can use [value presenters](value-presenters.md) to format cell content. `ValuePresenterInterface` implementations
transform raw values before display. The built-in `SimpleValuePresenter` handles `null`, booleans, dates, and enums:

```php
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;

new DataColumn(
    property: 'status',
    header: 'Status',
    content: new SimpleValuePresenter(
        null: 'Unknown',
        true: 'Active',
        false: 'Inactive',
    ),
)
```

#### Dynamic body attributes

The `bodyAttributes` parameter accepts either a static array or a callable that returns attributes per row:

```php
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

new DataColumn(
    property: 'price',
    bodyAttributes: static fn(object $data, DataContext $context): array => [
        'class' => $data->price > 100 ? 'high-price' : 'low-price',
    ],
)
```

#### Dynamic body CSS class

The `bodyClass` parameter also supports callables:

```php
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

new DataColumn(
    property: 'status',
    bodyClass: static fn(object $data, DataContext $context): string => $data->isActive() ? 'active' : 'inactive',
)
```

#### Disabling sorting

Sorting is enabled by default (`withSorting: true`). To disable it for a column:

```php
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

new DataColumn(
    property: 'description',
    withSorting: false,
)
```

### ActionColumn

`ActionColumn` displays action buttons (view, update, delete by default).

**Constructor parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `template` | `?string` | `null` | Template for button layout, e.g. `'{view} {update}'` |
| `before` | `?string` | `null` | Content prepended to the action column content |
| `after` | `?string` | `null` | Content appended to the action column content |
| `urlConfig` | `mixed` | `null` | URL configuration for generating button URLs |
| `urlCreator` | `?callable` | `null` | Callback: `fn(string $action, DataContext $context): string` |
| `header` | `?string` | `null` | Header cell content |
| `footer` | `?string` | `null` | Footer cell content |
| `content` | `mixed` | `null` | Custom content (overrides buttons) |
| `buttons` | `?array` | `null` | Array of buttons. Values are `ActionButton` or `callable(string $url): string` |
| `visibleButtons` | `?array` | `null` | Button visibility: `['view' => true, 'delete' => fn($data, $key, $index): bool => ...]` |
| `columnAttributes` | `array` | `[]` | HTML attributes for column cells |
| `headerAttributes` | `array` | `[]` | HTML attributes for the header cell |
| `bodyAttributes` | `array` | `[]` | HTML attributes for body cells |
| `footerAttributes` | `array` | `[]` | HTML attributes for the footer cell |
| `visible` | `bool` | `true` | Whether the column is visible |

Default buttons (when `buttons` is `null`) are `view`, `update`, and `delete`.

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

new ActionColumn(
    urlCreator: static fn(string $action, DataContext $context): string => "/posts/$action/" . $context->data->getId(),
)
```

#### Custom buttons with ActionButton

`ActionButton` defines individual action buttons with these parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `content` | `Closure\|string\|Stringable` | `''` | Button label. Closure: `fn(array\|object $data, DataContext $context): string\|Stringable` |
| `url` | `Closure\|string\|null` | `null` | Button URL. Closure: `fn(array\|object $data, DataContext $context): string`. If `null`, the column's `urlCreator` is used |
| `attributes` | `Closure\|array\|null` | `null` | HTML attributes. Closure: `fn(array\|object $data, DataContext $context): array` |
| `class` | `Closure\|string\|array\|false\|null` | `false` | CSS class(es). `false` means use only the renderer's default class. Closure: `fn(array\|object $data, DataContext $context): string\|array\|null` |
| `title` | `?string` | `null` | The `title` HTML attribute |
| `overrideAttributes` | `bool` | `false` | If `true`, button attributes replace (instead of merge with) the renderer's default attributes |

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionButton;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

new ActionColumn(
    buttons: [
        'view' => new ActionButton(
            content: 'View',
            url: static fn(array|object $data, DataContext $context): string => '/posts/view/' . $context->key,
            title: 'View this post',
            class: 'btn btn-info',
        ),
        'delete' => new ActionButton(
            content: 'Delete',
            title: 'Delete this post',
            attributes: static fn(array|object $data, DataContext $context): array => [
                'data-confirm' => 'Are you sure you want to delete ' . $data->title . '?',
            ],
        ),
    ],
    urlCreator: static fn(string $action, DataContext $context): string => "/posts/$action/" . $context->key,
)
```

#### Buttons as callables

Instead of `ActionButton`, you can use a callable that receives the generated URL and returns HTML:

```php
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

new ActionColumn(
    buttons: [
        'view' => static fn(string $url): string => (string) Html::a('View', $url, ['class' => 'btn']),
    ],
    urlCreator: static fn(string $action, DataContext $context): string => "/posts/$action/" . $context->key,
)
```

#### Controlling button visibility

Use `visibleButtons` to show or hide buttons per row. Values can be `bool` or a closure:

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;

new ActionColumn(
    visibleButtons: [
        'view' => true,
        'update' => true,
        'delete' => static fn(array|object $data, mixed $key, int $index): bool => $data->canDelete(),
    ],
)
```

Buttons not listed in `visibleButtons` are hidden by default.

#### Custom template

The `template` parameter controls which buttons appear and in what order:

```php
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;

new ActionColumn(
    template: '{view} | {update}',
)
```

### CheckboxColumn

`CheckboxColumn` adds checkboxes for row selection.

**Constructor parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `header` | `?string` | `null` | Header content. If `null` and `multiple` is `true`, a "select all" checkbox is rendered |
| `footer` | `?string` | `null` | Footer content |
| `columnAttributes` | `array` | `[]` | HTML attributes for column cells |
| `headerAttributes` | `array` | `[]` | HTML attributes for the header cell |
| `bodyAttributes` | `array` | `[]` | HTML attributes for body cells |
| `inputAttributes` | `array` | `[]` | HTML attributes for checkbox inputs |
| `name` | `?string` | `null` | The `name` attribute for checkboxes |
| `multiple` | `bool` | `true` | Whether to allow multiple selection (`name[]` vs `name`) |
| `content` | `?Closure` | `null` | Custom content: `fn(Checkbox $input, DataContext $context): string\|Stringable` |
| `visible` | `bool` | `true` | Whether the column is visible |

```php
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;

new CheckboxColumn(
    name: 'selection',
    multiple: true,
)
```

### RadioColumn

`RadioColumn` displays a column of radio buttons for single-row selection. Each radio button shares the
same `name` attribute, so only one can be selected at a time.

**Constructor parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `header` | `?string` | `null` | Header content. If `null`, no header is rendered |
| `footer` | `?string` | `null` | Footer content |
| `columnAttributes` | `array` | `[]` | HTML attributes for column cells |
| `headerAttributes` | `array` | `[]` | HTML attributes for the header cell |
| `bodyAttributes` | `array` | `[]` | HTML attributes for body cells |
| `inputAttributes` | `array` | `[]` | HTML attributes for radio inputs |
| `name` | `?string` | `null` | The `name` attribute for radio buttons. Defaults to `radio-selection` if not set |
| `content` | `?Closure` | `null` | Custom content callback |
| `visible` | `bool` | `true` | Whether the column is visible |

```php
use Yiisoft\Yii\DataView\GridView\Column\RadioColumn;

new RadioColumn(
    name: 'selected-item',
    header: 'Select',
)
```

### SerialColumn

`SerialColumn` displays sequential row numbers (1-based). When used with `OffsetPaginator`, the numbering
accounts for the current page offset.

**Constructor parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `header` | `?string` | `null` | Header content. Defaults to `#` |
| `footer` | `?string` | `null` | Footer content |
| `columnAttributes` | `array` | `[]` | HTML attributes for column cells |
| `bodyAttributes` | `array` | `[]` | HTML attributes for body cells |
| `visible` | `bool` | `true` | Whether the column is visible |

```php
use Yiisoft\Yii\DataView\GridView\Column\SerialColumn;

new SerialColumn()
```

With custom header:

```php
use Yiisoft\Yii\DataView\GridView\Column\SerialColumn;

new SerialColumn(header: 'No.')
```

## Filtering

GridView supports column filtering. Of the built-in columns, only `DataColumn` supports it. For example:

```php
/**
 * @var \Yiisoft\Data\Reader\ReadableDataInterface $dataReader
 */
echo GridView::widget()
    ->dataReader($dataReader)
    ->columns(
        new DataColumn(
            property: 'name',
            filter: true, // Enable filtering (text input widget is used by default)
            filterValidation: new Length(max: 50), // Validation rules for filter value
        ),
        new DataColumn(
            property: 'type',
            filter: ['on' => 'Enabled', 'off' => 'Disabled'], // Filter by predefined list of values
            filterValidation: new In(['on', 'off']),
        ),
    );
```

> If a property name in the URL has the same name as pagination or sort parameters, you should choose different names for those
> parameters (see [URLs](./urls.md)).

### `GridView` filter options

- `filterCellAttributes(array $attributes)` - HTML attributes for the filter cell (`td`) tag.
- `filterCellInvalidClass(?string $class)` - CSS class for the filter cell when the filter value is invalid.
- `filterErrorsContainerAttributes(array $attributes)` - HTML attributes for the filter errors container.
- `filterFormId(string $id)` - set a custom ID for the filter form.
- `filterFormAttributes(array $attributes)` - HTML attributes for the filter form tag.

### `DataColumn` filter parameters

#### `$filter`

Available values:

- `false` - no filter;
- `true` - text input;
- `array` - dropdown list (select) with these options;
- `\Yiisoft\Yii\DataView\Filter\Widget\FilterWidget` instance — custom filter widget.

Filter widgets out of the box:

- `\Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter` - text input;
- `\Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter` - dropdown list (select).

#### `$filterFactory`

Available values:

- `null` - if `$filter` is array, then `EqualsFilterFactory` is used, otherwise `LikeFilterFactory`;
- class name — filter factory will be resolved from the container;
- `\Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface` instance — custom filter factory.

Filter factories out of the box:

- `\Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory` - creates `Equals` data filter;
- `\Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory` - creates `Like` data filter.

#### `$filterValidation`

Set [validation rules](https://github.com/yiisoft/validator/tree/master/docs/guide/en#rules) for the filter value.

Available values:

- `null` - without validation;
- `array` - list of validation rules;
- `\Yiisoft\Validator\RuleInterface` instance — single validation rule.

#### `$filterEmpty`

Condition for determining when a filter value is empty. If the value is empty, the filter is ignored.

Available values:

- `null` or `true` - `\Yiisoft\Validator\EmptyCondition\WhenEmpty` is used, empty values: `null`, `[]`, or `''`;
- `false` - `\Yiisoft\Validator\EmptyCondition\NeverEmpty` is used, every value is considered non-empty;
- `callable` - custom condition with signature `callable(mixed $value): bool`.

## Sorting

You can customize sorting behavior and display:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    // Keep current page when sorting
    ->keepPageOnSort(true)

    // Add sort direction indicators next to the column header
    ->sortableHeaderAscAppend(' &uarr;')
    ->sortableHeaderDescAppend(' &darr;')

    // Add content before sort indicators
    ->sortableHeaderAscPrepend('')
    ->sortableHeaderDescPrepend('')

    // Customize sortable link attributes
    ->sortableLinkAttributes(['class' => 'sort-link'])

    // Enable multi-column sorting (users can sort by several columns at once)
    ->multiSort()

    ->columns(
        new DataColumn(property: 'name'),               // sortable by default
        new DataColumn(property: 'email'),               // sortable by default
        new DataColumn(property: 'bio', withSorting: false), // not sortable
    )
?>
```

### Sorting methods

| Method | Description |
|--------|-------------|
| `keepPageOnSort(bool $enabled = true)` | Keep current page when sort changes |
| `multiSort(bool $enable = true)` | Allow sorting by multiple columns at once |
| `sortableHeaderPrepend(string\|Stringable $content)` | Prepend content to all sortable column headers |
| `sortableHeaderAppend(string\|Stringable $content)` | Append content to all sortable column headers |
| `sortableHeaderAscPrepend(string\|Stringable $content)` | Prepend content to ascending sorted headers |
| `sortableHeaderAscAppend(string\|Stringable $content)` | Append content to ascending sorted headers |
| `sortableHeaderDescPrepend(string\|Stringable $content)` | Prepend content to descending sorted headers |
| `sortableHeaderDescAppend(string\|Stringable $content)` | Append content to descending sorted headers |
| `sortableLinkAttributes(array $attributes)` | HTML attributes for sortable header links |

> The `sortableHeaderClass`, `sortableHeaderAscClass`, `sortableHeaderDescClass`, `sortableLinkAscClass`,
> and `sortableLinkDescClass` properties can be set via [widget theming](themes.md).

## Container Customization

The entire GridView output is wrapped in a container element (a `<div>` by default).

| Method | Description |
|--------|-------------|
| `containerTag(?string $tag)` | Set the container tag (`null` to disable container wrapping) |
| `containerAttributes(array $attributes)` | HTML attributes for the container |
| `containerClass(BackedEnum\|string\|null ...$class)` | Replace container CSS classes |
| `addContainerClass(BackedEnum\|string\|null ...$class)` | Add CSS classes to the container |
| `id(string $id)` | Set the container `id` attribute |

```php
<?= GridView::widget()
    ->dataReader($dataReader)
    ->containerTag('section')
    ->id('users-grid')
    ->addContainerClass('data-grid', 'my-grid')
    ->columns(/* ... */)
?>
```

## Header Customization

This is the header section above the grid table (not the table header row). It is part of the layout template.

| Method | Description |
|--------|-------------|
| `header(string $content)` | Set the header content |
| `headerTag(?string $tag)` | Set the header tag (`null` to disable) |
| `headerAttributes(array $attributes)` | HTML attributes for the header element |
| `headerClass(BackedEnum\|string\|null ...$class)` | Replace header CSS classes |
| `addHeaderClass(BackedEnum\|string\|null ...$class)` | Add CSS classes to the header element |
| `encodeHeader(bool $encode)` | Whether to HTML-encode the header content |

```php
<?= GridView::widget()
    ->dataReader($dataReader)
    ->header('User List')
    ->headerTag('h2')
    ->addHeaderClass('grid-title')
    ->columns(/* ... */)
?>
```

## Toolbar

The `toolbar()` method sets HTML content for the toolbar section of the layout:

```php
<?= GridView::widget()
    ->dataReader($dataReader)
    ->toolbar('<div class="grid-toolbar"><button>Export</button></div>')
    ->columns(/* ... */)
?>
```

The toolbar is placed according to the layout template. The default layout is:
`{header}\n{toolbar}\n{items}\n{summary}\n{pager}\n{pageSize}`.

You can change the layout with the `layout()` method.

## Table Structure

### Table header and footer

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    // Enable/disable table header and footer
    ->enableHeader(true)
    ->enableFooter(true)

    // Customize row attributes
    ->headerRowAttributes(['class' => 'header-row'])
    ->footerRowAttributes(['class' => 'footer-row'])
    ->columns(/* ... */)
?>
```

### Table and body attributes

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    // Table attributes
    ->tableAttributes(['class' => 'grid-table'])
    ->addTableClass('table', 'table-striped')

    // Tbody attributes
    ->tbodyAttributes(['class' => 'grid-body'])

    // Default cell attributes
    ->headerCellAttributes(['class' => 'header-cell'])
    ->bodyCellAttributes(['class' => 'body-cell'])
    ->columns(/* ... */)
?>
```

### Caption

Add a caption to the grid table:

```php
use Yiisoft\Yii\DataView\GridView\GridView;

echo GridView::widget()
    ->dataReader($dataReader)
    ->caption('List of Users')
    ->columns(/* ... */);
```

The `caption()` method accepts `string`, `Stringable`, or `null`. Pass `null` to remove the caption.
The caption is rendered as a `<caption>` tag inside the `<table>` element.

### Column grouping

Enable HTML `<colgroup>` rendering. When enabled, each column renderer's `renderColumn()` method is called
to produce a `<col>` tag with the column's `columnAttributes`:

```php
echo GridView::widget()
    ->dataReader($dataReader)
    ->columnGrouping()
    ->columns(
        new DataColumn(property: 'name', columnAttributes: ['style' => 'width: 200px']),
        new DataColumn(property: 'email', columnAttributes: ['style' => 'width: 300px']),
    );
```

## Row Customization

```php
<?php
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView\BodyRowContext;
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    // Customize body row attributes
    ->bodyRowAttributes(static fn(object $data, BodyRowContext $context): array => [
        'class' => $context->index % 2 === 0 ? 'even' : 'odd',
    ])

    // Add content before/after rows
    ->beforeRow(static function (object $data, mixed $key, int $index, GridView $grid): ?\Yiisoft\Html\Tag\Tr {
        return $data->hasCategory()
            ? Html::tr()->content(Html::td($data->getCategory())->colspan(6))
            : null;
    })
    ->afterRow(static function (object $data, mixed $key, int $index, GridView $grid): ?\Yiisoft\Html\Tag\Tr {
        return $data->hasDetails()
            ? Html::tr()->content(Html::td($data->getDetails())->colspan(6))
            : null;
    })
    ->columns(/* ... */)
?>
```

The `bodyRowAttributes()` method accepts either an array or a `Closure`. When an array is passed, individual
attribute values can themselves be closures: `fn(array|object $data, BodyRowContext $context): mixed`.

## Empty Cell Customization

When a cell has no content, GridView renders an empty cell. You can customize its appearance:

| Method | Description |
|--------|-------------|
| `emptyCell(string $content, ?array $attributes = null)` | Set empty cell HTML content and optionally its attributes. Default content: `&nbsp;` |
| `emptyCellAttributes(array $attributes)` | Set HTML attributes for empty cells |

```php
echo GridView::widget()
    ->dataReader($dataReader)
    ->emptyCell('-', ['class' => 'empty'])
    ->columns(/* ... */);
```

## No Results Customization

When there is no data to display, the grid shows a "no results" message:

| Method | Description |
|--------|-------------|
| `noResultsText(string $text)` | The text message (subject to translation). Default: `'No results found.'` |
| `noResultsTemplate(string $template)` | Template for the no-results content. `{text}` is replaced with the translated text. Default: `'{text}'` |
| `noResultsCellAttributes(array $attributes)` | HTML attributes for the no-results table cell |

```php
echo GridView::widget()
    ->dataReader($dataReader)
    ->noResultsText('Nothing here yet.')
    ->noResultsCellAttributes(['class' => 'text-muted text-center'])
    ->columns(/* ... */);
```

## Page Not Found Handling

| Method | Description |
|--------|-------------|
| `ignoreMissingPage(bool $enabled)` | When `true` (default), if the requested page is not found, the first page is shown instead of throwing an exception |
| `pageNotFoundExceptionCallback(?callable $callback)` | Callback to execute when a page is not found and `ignoreMissingPage` is `false` |

## Custom Column Renderers

You can configure column renderers with custom constructor arguments:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
?>

<?= GridView::widget()
    ->dataReader($dataReader)
    ->addColumnRendererConfigs([
        CustomColumnRenderer::class => [
            'optionA' => 'valueA',
            'optionB' => 'valueB',
        ],
    ])
    ->columns(/* ... */)
?>
```

## Additional Features

Features shared by all list widgets:

- [Pagination](pagination.md)
- [URLs](urls.md)
- [Translation](translation.md)
- [Themes](themes.md)
- [Value Presenters](value-presenters.md)
