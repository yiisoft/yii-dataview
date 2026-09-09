# Accessibility

This guide collects the accessibility behavior of the data view widgets: what they do automatically for
assistive technologies (screen readers, braille displays, voice control), how to override it, and what
you still need to provide yourself.

## GridView

`GridView` renders a semantic HTML table so assistive technologies can announce it as a table and let
users navigate it by row and column.

### What `GridView` does automatically

- The table is split into `<thead>`, `<tbody>`, and (when enabled) `<tfoot>` sections.
- Every header cell is a `<th>` element carrying `scope="col"`, so screen readers announce the corresponding column
  header when the user moves through the body cells.
- Every sortable header cell carries an `aria-sort` attribute reflecting the current sort state: `ascending` or
  `descending` on the column that is sorted, and `none` on the other sortable columns.

### Overriding or removing `scope="col"`

The `scope="col"` attribute is a default value that can be overridden or removed through the regular header cell
attribute methods. Pass `['scope' => null]` to drop it, or another value (for example `'colgroup'`) to change it:

```php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

echo GridView::widget()
    ->dataReader($dataReader)
    // For all header cells:
    ->headerCellAttributes(['scope' => null])
    ->columns(
        // ...or for a single column:
        new DataColumn(property: 'name', headerAttributes: ['scope' => 'colgroup']),
    );
```

### Row headers

When one column identifies each row (a name, a title, an ID), mark it as a row header with the `DataColumn`
`rowHeader` parameter. Its body cells are then rendered as `<th scope="row">` instead of `<td>`, so screen readers
announce that value together with the column header when the user moves across the row.

```php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

echo GridView::widget()
    ->dataReader($dataReader)
    ->columns(
        new DataColumn(property: 'name', rowHeader: true),
        new DataColumn(property: 'email'),
        new DataColumn(property: 'createdAt'),
    );
```

The `scope="row"` attribute is a default value. Change or remove it through the column's `bodyAttributes`, for
example `bodyAttributes: ['scope' => 'rowgroup']` or `bodyAttributes: ['scope' => null]`. An empty cell renders as a
plain `<th>` without `scope` — there is no value to label the row with.

### Overriding `aria-sort`

The automatically added `aria-sort` value is only used when the header cell does not already have the attribute. To
change or remove it for a column, set `aria-sort` in the column's `headerAttributes`:

```php
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

echo GridView::widget()
    ->dataReader($dataReader)
    ->columns(
        new DataColumn(property: 'name', headerAttributes: ['aria-sort' => 'other']),
    );
```

### Recommendations

- Give the table an accessible name with `caption()`. It is exposed to assistive technologies and helps users tell
  several tables on a page apart.
- Provide meaningful header text for every column. When a `DataColumn` has no `header`, the property name is used as
  a fallback, which is rarely a good label.
- For sortable columns you can give the sort links a clearer action label with
  `sortableLinkAttributes(['aria-label' => 'Sort by name'])`.

## Pagination

Both pagination widgets (`OffsetPagination` and `KeysetPagination`) render an HTML `nav` landmark with a list of
links, so assistive technologies can announce and navigate the control.

### What the widgets do automatically

- `OffsetPagination` adds `aria-current="page"` to the `<a>` element of the current page, so screen
  readers announce which page is active.
- Both widgets add `aria-disabled="true"` to links that are currently not actionable: the "first" and
  "previous" links on the first page, and the "next" and "last" links on the last page (`KeysetPagination`
  renders these without an `href`).

### Overriding or removing the attributes

`aria-current` and `aria-disabled` are default values applied only when the key is not already present
in `linkAttributes()`. Set the key there to change the value, or pass `false` / `null` to drop it:

```php
use Yiisoft\Yii\DataView\GridView\GridView;

echo GridView::widget()
    ->dataReader($paginator)
    ->offsetPaginationConfig([
        'linkAttributes()' => [['aria-disabled' => false]],
    ]);
```

### Recommendations

The widgets do not set these for you:

- Give the `nav` an accessible name so several navigation landmarks on a page can be told apart, for
  example `containerAttributes(['aria-label' => 'Pagination'])`.
- The default first/previous/next/last labels are bare glyphs (`⟪ ⟨ ⟩ ⟫`). Replace them with text
  labels (`labelPrevious('Previous')`, ...), or pass a `Stringable` label that carries its own
  `aria-label`, so the purpose of each link is announced.
