# Detail View

The `DetailView` widget is designed to display details about a single data item.
It provides a flexible way to render detailed views with customizable attribute layouts.

## Basic Usage

The basic usage is the following:

```php
<?php
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\DetailView\DataField;
?>

<?= DetailView::widget()
    ->data(['id' => 1, 'username' => 'tests 1', 'status' => true])
    ->fields(
        new DataField('id'),
        new DataField('username', label: 'Name of the user'),
        new DataField('status'),
    )
?>
```

In the above the data set via `data()` can be either an object with public fields or an associative array.
What's displayed is defined via fields configurations where each is an instance of `DataField` that may contain
the following constructor parameters:

- `property` - Property name in the data object or key name in the data array. Optional if `value` is set explicitly.
- `label` - Field label. If not set, `property` is used.
- `labelEncode` - Whether the label should be HTML encoded (default: `true`).
- `labelAttributes` - An array of label's HTML attributes or a function accepting a `LabelContext` and returning
  the array.
- `value` - Explicit value. If `null`, the value is obtained from the data by its property name. Could be a
  function accepting a `GetValueContext` and returning the value.
- `valueEncode` - Whether the value should be HTML encoded (default: `true`).
- `valueAttributes` - An array of value's HTML attributes or a function accepting a `ValueContext` and returning
  the array.
- `fieldAttributes` - An array of field container's HTML attributes or a function accepting a `FieldContext` and
  returning the array.
- `visible` - Whether the field is visible (default: `true`). Hidden fields are not rendered in the output.

## Rendering options

The widget is fully customizable in terms of how it's rendered.

### Common Structure

By default, the widget is rendered with this structure:

```
{start container tag}
    {prepend}
    {start list tag}
        {start field tag}
            {field prepend}
            {field content}
            {field append}
        {end field tag}
        …
    {end list tag}
    {append}
{end container tag}
```

The container can be customized with:

- `containerTag()` - set the container HTML tag (default: `null` - no container tag);
- `containerAttributes()` - set attributes for the container tag;
- `prepend()` - add content after the opening container tag;
- `append()` - add content before the closing container tag.

The field list can be customized with:

- `listTag()` - set the list HTML tag (default: `dl`, set to `null` to disable list wrapper);
- `listAttributes()` - set attributes for the list tag.

### Field Structure

Each field uses this template by default:

```
{label} {value}
```

Field rendering can be customized with:

- `fieldTemplate()` - set the field template (available placeholders: `{label}`, `{value}`);
- `fieldTag()` - set the field container HTML tag (default: `null` - no wrapper);
- `fieldAttributes()` - set attributes for the field container (array or closure accepting `FieldContext`);
- `fieldPrepend()` - add content after the opening field tag;
- `fieldAppend()` - add content before the closing field tag.

### Label Rendering

Labels are rendered as:

```
{start label tag}
    {label prepend}
    {label}
    {label append}
{end label tag}
```

Label rendering can be customized with:

- `labelTag()` - set the label HTML tag (default: `dt`, set to `null` to disable wrapper);
- `labelAttributes()` - set attributes for the label tag (array or closure accepting `LabelContext`);
- `labelPrepend()` - add content before the label;
- `labelAppend()` - add content after the label.

If `DataField`'s `label` is set, it is used as is, otherwise it falls back to `property`.

### Value Rendering

Values are rendered as:

```html
{start value tag}
    {value prepend}
    {value}
    {value append}
{end value tag}
```

Value rendering can be customized with:

- `valueTag()` - set the value HTML tag (default: `dd`, set to `null` to disable wrapper);
- `valueAttributes()` - set attributes for the value tag (array or closure accepting `ValueContext`);
- `valuePrepend()` - add content before the value;
- `valueAppend()` - add content after the value.

If `DataField`'s `value` is set, it is used as is, otherwise the value is retrieved from the data using `property` 
as either object property name or array key.

### Value Presentation

Values are processed through a value presenter before rendering. By default, `SimpleValuePresenter` is used, which 
handles basic type conversion (e.g., boolean values to "True" / "False" strings).

You can customize value presentation with:

- `valuePresenter()` - set a custom value presenter implementing `ValuePresenterInterface`.
