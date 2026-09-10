# Accessibility

This guide collects the accessibility behavior of the data view widgets: what they can add for assistive
technologies (screen readers, braille displays, voice control), how to override it, and what you still need
to provide yourself.

## Enabling the automatic attributes

`GridView` and `ListView` do not add accessibility attributes by default. Call `accessibility()` to opt in —
rendering then adds `scope="col"` and `aria-sort` on header cells, `scope="row"` on row header cells, and
`aria-current`/`aria-disabled` on pagination links:

```php
use Yiisoft\Yii\DataView\GridView\GridView;

echo GridView::widget()
    ->dataReader($dataReader)
    ->accessibility();
```

Pass `accessibility(false)` to turn it back off. The rest of this guide describes what the option adds and
how to fine-tune the individual attributes; all of it applies only when `accessibility()` is enabled.

## GridView

`GridView` renders a semantic HTML table so assistive technologies can announce it as a table and let
users navigate it by row and column.

### What `GridView` adds with `accessibility()` enabled

- The table is always split into `<thead>`, `<tbody>`, and (when enabled) `<tfoot>` sections, regardless of the
  option.
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
`rowHeader` parameter. Its body cells are then rendered as `<th>` instead of `<td>`, and with `accessibility()`
enabled they also carry `scope="row"`, so screen readers announce that value together with the column header when
the user moves across the row.

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
example `bodyAttributes: ['scope' => 'rowgroup']` or `bodyAttributes: ['scope' => null]`.

An empty cell renders as a plain `<th>` without the generated `scope="row"`: there is no value to label the row
with, so pointing assistive technology at it would only add noise. This is intentional and independent of
`keepColumnAttributesInEmptyCell()` — that method controls whether the column's own body cell attributes are kept
on the placeholder cell, and a `scope` you set explicitly through `bodyAttributes` is still applied to empty cells
when it is enabled.

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

### What the widgets add with `accessibility()` enabled

When driven by `GridView`/`ListView`, the pagination widgets follow the view's `accessibility()` setting. Used
directly, they take the flag from `PaginationContext` (also exposed as the `$accessibility` argument of
`OffsetPagination::create()` and `KeysetPagination::create()`).

- `OffsetPagination` adds `aria-current="page"` to the `<a>` element of the current page, so screen
  readers announce which page is active.
- Both widgets add `aria-disabled="true"` to links that are currently not actionable: the "first" and
  "previous" links on the first page, and the "next" and "last" links on the last page (`KeysetPagination`
  renders these without an `href`).
- Both widgets add an `aria-label` to the `nav` container and to every page link, so several navigation
  landmarks on a page can be told apart and the purpose of each link (whose visible content is a bare glyph
  or a bare number) is announced. The default texts are: `Pagination` on the `nav`; `First page`,
  `Previous page`, `Next page`, `Last page` on the corresponding links; and `Page {page}` (with the number
  substituted for `{page}`) on the numbered links of `OffsetPagination`.

### Translating the `aria-label` texts

When the pagination is rendered through `GridView`/`ListView`, the `aria-label` texts are passed through the
view's translator using the `yii-dataview` category, so a translation supplied for `First page`,
`Previous page`, `Next page`, `Last page`, `Page {page}` or `Pagination` is used automatically. Used directly,
the widgets emit the English defaults unless a translator is passed — either as the `$translator` argument of
`OffsetPagination::create()` / `KeysetPagination::create()`, or through a custom `PaginationContext`.

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

The `nav` and link `aria-label` texts are configured with dedicated methods — `ariaLabelNav()`,
`ariaLabelFirst()`, `ariaLabelPrevious()`, `ariaLabelNext()`, `ariaLabelLast()` and `ariaLabelPage()` on
`OffsetPagination` (`KeysetPagination` has `ariaLabelNav()`, `ariaLabelPrevious()` and `ariaLabelNext()`).
Pass a string to change the text, or `null` to omit that `aria-label`:

```php
use Yiisoft\Yii\DataView\GridView\GridView;

echo GridView::widget()
    ->dataReader($paginator)
    ->offsetPaginationConfig([
        'ariaLabelPage()' => ['Go to page {page}'],
        'ariaLabelNav()' => [null],
    ]);
```

An `aria-label` already present in `containerAttributes()` (for the `nav`) or `linkAttributes()` (for the
links) is kept as is and takes precedence over these methods.
