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
the following:

- `name` - Property name in the data object or key name in the data array. Optional if `value` is set explicitly.
- `label` - Field label. If not set, `name` is used.
- `labelAttributes` - An array of label's HTML attribute values indexed by attribute names or a function accepting
  data and returning the array.
- `labelTag` - Label HTML tag.
- `value` Explicit value. If `null`, the value is obtained from the data by its name. Could be a
  function accepting data as an argument and returning the value.
- `valueTag` Value HTML tag.
- `valueAttributes` - An array of value's HTML attribute values indexed by attribute names or a function accepting
  data and returning the array.

## Rendering options

The widget is fully customizable in terms of how it's rendered.

### Main widget

By default, the widget is rendered as

```html
<div{attributes}>
    {header}
    <dl{fieldListAttributes}>
        {fields}
    </dl>
</div>
```

The template above could be changed with `template()`.
Attributes for the main widget tag could be set using `attributes()` and attributes for the field list container
could be set with `fieldListAttributes()`. 
Header is an extra content displayed and could be set with `header()`.

`{fields}` placeholder is replaced with the rendered fields. Each field is using a template that defaults to the following:

```html
<div{attributes}>
    {label}
    {value}
</div>
```

The template could be customized with `fieldTemplate()` and attributes could be set with `fieldAttributes()`.


Label is rendered as:

```html
<{tag}{attributes}>{label}</{tag}>
```

The template above could be customized with `labelTemplate()`.

Tag and its attributes could be set with `labelTag()` or `labelAttributes()` but if `DataField` has corresponding
properties set, they have higher priority.

If `DataField`'s `label` is set, it is used as is else it falls back to `name`.

The value is similar. It is rendered as:

```html
<{tag}{attributes}>{value}</{tag}>
```

The template above could be customized with `valueTemplate()`.

Tag and its attributes could be set with `valueTag()` or `valueAttributes()` but if `DataField` has corresponding
properties set, they have higher priority. 

If `DataField`'s `value` is set, it is used as is else it is getting the value from the data using `name` as either
object property name or array key.

There are two extra methods, `valueTrue()` and `valueFalse()`. You can use these to define how to display a boolean
value.
