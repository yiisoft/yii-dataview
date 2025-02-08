# Grid View

GridView is a powerful data presentation widget that displays data in a highly customizable grid format.
It supports features such as:

- Pagination
- Sorting
- Filtering
- Custom column rendering
- Action buttons
- Row customization
- Header and footer sections

## Basic Usage

Here's a basic example of using GridView:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\DataReader\DataReaderInterface;

$dataReader = new DataReader($query);
?>


<?= GridView::widget()
    ->data($dataReader)
    ->columns(
        new DataColumn(property: 'id'),
        new DataColumn(property: 'title', header: 'Post Title'),
        new DataColumn(property: 'created_at')
    )
?>
```

It is getting data from data reader and rendering it according to configuration passed in `columns()`.

## Column Types

GridView supports several types of columns out of the box.

### Data Column

DataColumn is the most commonly used column type. It displays model attribute values:

```php
use Yiisoft\Yii\DataView\Column\DataColumn;

$column = new DataColumn(
    property: 'title',
    header: 'Post Title',
    withSorting: true
)
```

### Action Column

ActionColumn displays action buttons (e.g., view, edit, delete):

```php
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Html\Html;

$column = new ActionColumn(
    buttons: [
        'view' => new \Yiisoft\Yii\DataView\Column\ActionButton(
            'View',
            static function (array|object $data, DataContext $context) { 
                return '/posts/view' . $data->id; 
            }
        ),
        'edit' => function (string $url): string {
            return (string) Html::a('Edit', $url);
        },
        'delete' => function (string $url): string {
            return (string) Html::a('Delete', $url);
        },
    ],
    urlCreator: function ($action, $model) {
        return "/posts/$action/" . $model->getId();
    },
);
```

### Checkbox Column

CheckboxColumn adds checkboxes for row selection:

```php
use Yiisoft\Yii\DataView\Column\CheckboxColumn;

$column = new CheckboxColumn(
    name: 'selection',
    multiple: true,
);
```

## Filtering

// TODO: fix it!

GridView supports filtering data through several configuration options:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
?>

<?= GridView::widget()
    // Set attributes for filter cells
    ->filterCellAttributes(['class' => 'filter-cell'])
    
    // Set class for cells with invalid filter values
    ->filterCellInvalidClass('invalid-filter')
    
    // Configure filter error container
    ->filterErrorsContainerAttributes(['class' => 'filter-errors'])
?>
```

## Sorting

You can customize sorting behavior and rendering:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
?>

<?= GridView::widget()
    // Keep current page when sorting
    ->keepPageOnSort(true) 
    
    // Add sort indicators
    ->sortableHeaderAscAppend('↑')
    ->sortableHeaderDescAppend('↓')
?>
```

## Layout Customization

GridView offers extensive layout customization.

### Header and Footer

```php
<?php
use Yiisoft\Yii\DataView\GridView;
?>

<?= GridView::widget()
    // Enable/disable header and footer
    ->enableHeaderTable(true)
    ->enableFooter(true)
    
    // Customize attributes
    ->headerRowAttributes(['class' => 'header-row'])
    ->footerRowAttributes(['class' => 'footer-row'])
?>
```

### Table Structure

```php
<?php
use Yiisoft\Yii\DataView\GridView;
?>

<?= GridView::widget()
    // Table attributes
    ->tableAttributes(['class' => 'grid-table'])
    
    // Body attributes
    ->tbodyAttributes(['class' => 'grid-body'])
    
    // Cell attributes
    ->headerCellAttributes(['class' => 'header-cell'])
    ->bodyCellAttributes(['class' => 'body-cell'])
?>
```

### Row Customization

```php
<?php
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Html\Html;
?>

<?= GridView::widget()
    // Customize body row attributes
    ->bodyRowAttributes(function ($model, $key, $index) {
        return ['class' => $index % 2 === 0 ? 'even' : 'odd'];
    })
    
    // Add content before/after rows
    ->beforeRow(function ($model, $key, $index) {
        return $model->hasCategory() 
            ? Html::tr()->content(Html::td($model->getCategory())->colspan(6))
            : null;
    })
    ->afterRow(function ($model, $key, $index) {
        return $model->hasDetails()
            ? Html::tr()->content(Html::td($model->getDetails())->colspan(6))
            : null;
    })
?>
```

## Custom Column Renderers

You can add custom column renderers for special rendering needs:

```php
<?php
use Yiisoft\Yii\DataView\GridView;
?>


<?= GridView::widget()
    ->addColumnRendererConfigs([
        CustomColumnRenderer::class => [
            'optionA' => 'valueA',
            'optionB' => 'valueB',
        ],
    ])
?>
```

## Additional Features

There are additional features common among all list widgets:

- [Pagination](pagination.md)
- [URLs](urls.md)
- [Translation](translation.md)
- [Themes](themes.md)
