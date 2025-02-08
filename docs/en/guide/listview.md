# List View

The `ListView` widget is designed to display a list of data items.
It provides a flexible way to render data in a list format,
with support for pagination, sorting, and custom item rendering.

## Basic Usage

The basic usage is the following:

```php
<?php
use Yiisoft\Yii\DataView\ListView;
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
- `$key`: Key associated with the item.
- `$index`: Zero-based index of the item.
- `$widget`: List view widget instance.

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

Alternatively, a callback could be used:

```php
<?php
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\ListView;
?>

<?= ListView::widget()
    ->itemCallback(function ($data, $key, int $index, ListView $widget): string {
        return '★' . Html::encode($data['id']) . ' - ' . Html::encode($data['name']);
    })
    ->dataReader($dataReader)
?>
```

## Rendering options

The widget rendering could be customized.

### Item list

Item list is rendered as `<ul>` by default and there's no separator between items.
It can be changed:

```php
<?php
use Yiisoft\Yii\DataView\ListView;
?>

<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->itemListTag('ol')
    ->itemListAttributes(['class' => 'my-list'])
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

Besides using a template or a callback, you can customize item rendering:

```php
<?php
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\ListItemContext $context;
?>

<?= ListView::widget()
    ->itemView('myitem.php')
    ->dataReader($dataReader)
    ->itemListTag('section')
    ->itemTag('div')
    ->itemListAttributes(['class' => 'item'])
    ->itemViewParameters(['time' => time()])
    ->beforeItem(function (ListItemContext $context): ?string {
        return $context->data['important'] ? '! ' : '';
    })
    ->afterItem(function (ListItemContext $context): ?string {
        return $context->data['expired'] ? ' ×' : '';
    })
?>
```

The above will result in:

```html
<div>
    <section>
        <div class="item">
            ! 1 - Bread
        </div> 
        <div class="item">
            2 - Milk ×
        </div>
    </section>
    <div>Page <b>1</b> of <b>1</b></div>
</div>
```

### Additional customization

As for every data widget that renders a set of data, you can additionally customize it with: 

- [Pagination](pagination.md)
- [URLs](urls.md)
- [Translation](translation.md)
- [Themes](themes.md)
