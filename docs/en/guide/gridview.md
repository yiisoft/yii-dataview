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
use Yiisoft\Yii\DataView\DataReader\DataReaderInterface;use Yiisoft\Yii\DataView\GridView\Column\DataColumn;use Yiisoft\Yii\DataView\GridView\GridView;

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

`DataColumn` is the most commonly used column type. It displays model attribute values:

```php
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;

$column = new DataColumn(
    property: 'title',
    header: 'Post Title',
    withSorting: true
)
```

### Action Column

ActionColumn displays action buttons (e.g., view, edit, delete):

```php
use Yiisoft\Html\Html;use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;

$column = new ActionColumn(
    buttons: [
        'view' => new \Yiisoft\Yii\DataView\GridView\Column\ActionButton(
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
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;

$column = new CheckboxColumn(
    name: 'selection',
    multiple: true,
);
```

## Filtering

GridView supports filtering. Of the built-in columns, only `DataColumn` supports it. For example:

```php
/** 
 * @var \Yiisoft\Data\Reader\DataReaderInterface $reader 
 */
echo GridView::widget()
    ->dataReader($reader)
    ->columns(
        new DataColumn(
            'name',
            filter: true, // Enable filtering (by default text input widget is used)
            filterValidation: new Length(max: 50), // Validation rules for filter value
        ),
        new DataColumn(
            'type',
            filter: ['on' => 'Enabled', 'off' => 'Disabled'], // Filtering by predefined list of values
            filterValidation: new In(['on', 'off']),
        ),
    );
```

> If a property name in the URL has the same name as pagination or sort parameters, you should choose different names for those
> parameters (see [URLs](./urls.md)).  

### `GridView` options

- `filterCellAttributes()` — set HTML attributes for the filter cell (`td`) tag.
- `filterCellInvalidClass()` — set CSS class for the filter cell when the filter is invalid. 
- `filterErrorsContainerAttributes()` — set HTML attributes for the container of filter errors.

### `DataColumn`

Of the built-in columns, only `DataColumn` supports filtering.

#### `$filter`

Available values:

- `false` — no filter;
- `true` — text input;
- `array` — dropdown list (select) with these options;
- `\Yiisoft\Yii\DataView\Filter\Widget\FilterWidget` instance — custom filter widget.

Filter widgets out of the box:

- `\Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter` — text input;
- `\Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter` — dropdown list (select).

#### `$filterFactory`

Available values:

- `null` — if `$filter` is array, then `LikeFilterFactory` is used, otherwise `EqualsFilterFactory`;
- class name — filter factory will be received from the container;
- `\Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface` instance — custom filter factory.

Filter factories out of the box:

- `\Yiisoft\Yii\DataView\Filter\Factory\EqualsFilterFactory` — create `Equals` data filter;
- `\Yiisoft\Yii\DataView\Filter\Factory\LikeFilterFactory` — create `Like` data filter.

#### `$filterValidation`

Set [validation rules](https://github.com/yiisoft/validator/tree/master/docs/guide/en#rules) for value of filter. 

Available values:

- `null` — without validation;
- `array` — list of validation rules.
- `\Yiisoft\Validator\RuleInterface` instance — single validation rule.

#### `$filterEmpty`

Set condition for empty value of filter. If value of filter is empty, then filter will be ignored.

Available values:

- `null` or `true` — `\Yiisoft\Validator\EmptyCondition\WhenEmpty` is used, empty values: `null`, `[]`, or `''`.
- `false` — `\Yiisoft\Validator\EmptyCondition\NeverEmpty` is used, every value is considered non-empty.
- `callable` — custom condition with signature `callable(mixed $value): bool`.

## Sorting

You can customize sorting behavior and rendering:

```php
<?php
use Yiisoft\Yii\DataView\GridView\GridView;
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
use Yiisoft\Yii\DataView\GridView\GridView;
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
use Yiisoft\Yii\DataView\GridView\GridView;
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
use Yiisoft\Html\Html;use Yiisoft\Yii\DataView\GridView\GridView;
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
use Yiisoft\Yii\DataView\GridView\GridView;
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
