# List View

The `ListView` widget displays a list of data items.
It renders data in a list format, with support for pagination, sorting, and custom item rendering.

## Basic Usage

The basic usage is the following:

```php
<?php
use Yiisoft\Yii\DataView\ListView\ListView;
?>

<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
?>
```

Data reader is usually an instance of paginator and `itemView` is a template like the following:


```php
<?php
use Yiisoft\Html\Html;

/** @var array $data */
?>

★ <?= Html::encode($data['id']) ?> - <?= Html::encode($data['name']) ?>
```

Here the following variables are available:

- `$data`: An item data.
- `$context`: A `ListItemContext` instance with the following properties:
  - `$context->data`: The current item data (same as `$data`).
  - `$context->key`: Key associated with the item.
  - `$context->index`: Zero-based index of the item.
  - `$context->widget`: List view widget instance.

Overall, it will produce HTML like this:

```html
<div>
    <ul>
        <li>
            ★ 1 - Bread
        </li>
        <li>
            ★ 2 - Milk
        </li>
    </ul>
    <div>Page <b>1</b> of <b>1</b></div>
</div>
```

Alternatively, a closure could be used via the `itemView()` method:

```php
<?php
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\ListView\ListItemContext;
?>

<?= ListView::widget()
    ->itemView(function (array $data, ListItemContext $context): string {
        return '★' . Html::encode($data['id']) . ' - ' . Html::encode($data['name']);
    })
    ->dataReader($dataReader)
?>
```

## Rendering options

The widget rendering could be customized.

### Item list

Item list is rendered as `<ul>` by default with a newline separator between items.
It can be changed:

```php
<?php
use Yiisoft\Yii\DataView\ListView\ListView;
?>

<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->listTag('ol')
    ->listAttributes(['class' => 'my-list'])
    ->separator(' ')
?>
```

The above will result in:

```html
<div>
    <ol class="my-list">
        <li>
            ★ 1 - Bread
        </li>
        <li>
            ★ 2 - Milk
        </li>
    </ol>
    <div>Page <b>1</b> of <b>1</b></div>
</div>
```

### Item

Besides using a template or a closure, you can customize item rendering:

```php
<?php
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\ListView\ListItemContext;
?>

<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->listTag('section')
    ->itemTag('div')
    ->listAttributes(['class' => 'items'])
    ->itemViewParameters(['time' => time()])
    ->beforeItem(function (array $data, ListItemContext $context): string {
        return $context->data['important'] ? '! ' : '';
    })
    ->afterItem(function (array $data, ListItemContext $context): string {
        return $context->data['expired'] ? ' ×' : '';
    })
?>
```

The above will result in:

```html
<div>
    <section class="items">
        <div>
            ! 1 - Bread
        </div>
        <div>
            2 - Milk ×
        </div>
    </section>
    <div>Page <b>1</b> of <b>1</b></div>
</div>
```

### Item attributes

You can set HTML attributes on each item tag. The `itemAttributes()` method accepts either a static
array or a closure that receives the item data and a `ListItemContext`:

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->itemAttributes(function (array $data, ListItemContext $context): array {
        return ['class' => 'item-' . $context->index];
    })
?>
```

When passing an array, individual attribute values can also be closures that receive a `ListItemContext`:

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->itemAttributes([
        'class' => static fn(ListItemContext $context) => "custom-class-{$context->data['id']}",
    ])
?>
```

### No results

When the data reader returns no items, a "no results" message is displayed.
You can customize the text, template, wrapping tag, and tag attributes:

- `noResultsText()` - sets the text shown when there are no results (default: `'No results found.'`). The text is
  passed through the translator.
- `noResultsTemplate()` - sets the template for the no-results block (default: `'{text}'`). The `{text}` token is
  replaced with the translated text.
- `noResultsTag()` - sets the HTML tag wrapping the no-results text (default: `'p'`). Pass `null` to disable
  the wrapper.
- `noResultsAttributes()` - sets HTML attributes for the no-results tag.

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->noResultsText('Nothing here yet.')
    ->noResultsTag('div')
    ->noResultsAttributes(['class' => 'empty-state'])
?>
```

### Container

The entire widget output is wrapped in a container tag. By default this is a `<div>`. You can
customize it with:

- `containerTag()` - set the container HTML tag (default: `'div'`). Pass `null` to disable the wrapper.
- `containerAttributes()` - set HTML attributes for the container tag.
- `containerClass()` - set CSS classes on the container (replaces existing classes).
- `addContainerClass()` - add CSS classes to the container (keeps existing classes).
- `id()` - shortcut to set the `id` attribute on the container.
- `prepend()` - add HTML content after the opening container tag.
- `append()` - add HTML content before the closing container tag.

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->containerTag('section')
    ->containerAttributes(['class' => 'list-wrapper'])
    ->id('product-list')
    ->prepend('<h2>Products</h2>')
?>
```

### Header

You can add a header section to the widget. The header is rendered inside the layout using
the `{header}` token.

- `header()` - set the header content.
- `headerTag()` - set the header HTML tag (default: `'div'`). Pass `null` to output raw content.
- `headerAttributes()` - set HTML attributes for the header tag.
- `headerClass()` - set CSS classes on the header tag (replaces existing classes).
- `addHeaderClass()` - add CSS classes to the header tag.
- `encodeHeader()` - whether to HTML-encode the header content (default: `true`).

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->header('Product List')
    ->headerTag('h1')
    ->headerAttributes(['class' => 'list-title'])
?>
```

### Layout

The `layout()` method controls how the different sections of the widget are arranged. The default layout is:

```
{header}\n{toolbar}\n{items}\n{summary}\n{pager}\n{pageSize}
```

Available tokens:

- `{header}` - the header section.
- `{toolbar}` - the toolbar content (set via `toolbar()`).
- `{items}` - the rendered list items.
- `{summary}` - the pagination summary (e.g., "Page 1 of 3").
- `{pager}` - the pagination widget.
- `{pageSize}` - the page size control.

You can rearrange or remove sections:

```php
<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->layout("{header}\n{items}\n{pager}")
?>
```

### Additional customization

As for every data widget that renders a set of data, you can additionally customize it with:

- [Pagination](pagination.md)
- [URLs](urls.md)
- [Translation](translation.md)
- [Themes](themes.md)
