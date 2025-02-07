# Theming

The Yii DataView component provides extensive theming capabilities through HTML attributes and CSS classes. You can customize the appearance of all components including GridView, pagination widgets, and individual elements.

## GridView Theming

### Table Structure

GridView allows you to customize the HTML structure and attributes of every table element:

```php
use Yiisoft\Yii\DataView\GridView;

echo GridView::widget()
    // Table attributes
    ->tableAttributes(['class' => 'table table-bordered'])
    ->tableClass('table', 'table-striped', 'table-hover')
    
    // Table body attributes
    ->tbodyAttributes(['class' => 'custom-tbody'])
    ->tbodyClass('tbody-default', 'tbody-responsive')
    
    // Row attributes
    ->headerRowAttributes(['class' => 'header-row'])
    ->bodyRowAttributes(['class' => 'body-row'])
    ->footerRowAttributes(['class' => 'footer-row'])
    
    // Cell attributes
    ->headerCellAttributes(['class' => 'header-cell'])
    ->bodyCellAttributes(['class' => 'body-cell'])
    
    // Empty cell configuration
    ->emptyCell('No data', ['class' => 'empty-cell'])
    ->emptyCellAttributes(['class' => 'empty-cell']);
```

### Dynamic Row Styling

You can apply dynamic styling to rows based on data:

```php
use Yiisoft\Html\Tag\Tr;

echo GridView::widget()
    ->bodyRowAttributes(function ($model, $context) {
        return [
            'class' => $model->status === 'active' ? 'table-success' : 'table-warning',
            'data-id' => $model->id,
        ];
    })
    ->beforeRow(function ($model, $key, $index, $grid) {
        if ($model->isSpecial) {
            return Tr::tag()
                ->class('special-row')
                ->content("Special item #$index");
        }
        return null;
    })
    ->afterRow(function ($model, $key, $index, $grid) {
        if ($model->hasDetails) {
            return Tr::tag()
                ->class('details-row')
                ->content($model->details);
        }
        return null;
    });
```

### Sortable Column Styling

Customize the appearance of sortable columns:

```php
echo GridView::widget()
    // Sortable link styling
    ->sortableLinkAttributes(['class' => 'sort-link'])
    
    // Sort indicators
    ->sortableHeaderPrepend('↕')
    ->sortableHeaderAppend('')
    ->sortableHeaderAscPrepend('↑')
    ->sortableHeaderAscAppend('')
    ->sortableHeaderDescPrepend('↓')
    ->sortableHeaderDescAppend('');
```

### Filter Styling

Style the filter section of the grid:

```php
echo GridView::widget()
    // Filter cell styling
    ->filterCellAttributes(['class' => 'filter-cell'])
    ->filterCellInvalidClass('is-invalid')
    
    // Error container styling
    ->filterErrorsContainerAttributes([
        'class' => 'invalid-feedback',
        'style' => 'display: block;'
    ]);
```

## Pagination Theming

Both offset and keyset pagination widgets support customization:

### Offset Pagination

```php
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

echo OffsetPagination::widget()
    // Container structure
    ->containerTag('nav')
    ->containerAttributes(['aria-label' => 'Page navigation'])
    
    // List structure
    ->listTag('ul')
    ->listAttributes(['class' => 'pagination'])
    
    // Item structure
    ->itemTag('li')
    ->itemAttributes(['class' => 'page-item'])
    ->currentItemClass('active')
    ->disabledItemClass('disabled')
    
    // Link styling
    ->linkAttributes(['class' => 'page-link'])
    ->currentLinkClass('current')
    ->disabledLinkClass('disabled')
    
    // Navigation labels
    ->labelPrevious('‹')
    ->labelNext('›')
    ->labelFirst('«')
    ->labelLast('»');
```

### Keyset Pagination

```php
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;

echo KeysetPagination::widget()
    // Container structure
    ->containerTag('nav')
    ->containerAttributes(['aria-label' => 'Navigation'])
    
    // List structure
    ->listTag('ul')
    ->listAttributes(['class' => 'pagination'])
    
    // Item structure
    ->itemTag('li')
    ->itemAttributes(['class' => 'page-item'])
    ->disabledItemClass('disabled')
    
    // Link styling
    ->linkAttributes(['class' => 'page-link'])
    ->disabledLinkClass('disabled')
    
    // Navigation labels
    ->labelPrevious('Previous')
    ->labelNext('Next');
```

## Integration with CSS Frameworks

### Bootstrap 5

Here's how to style your components for Bootstrap 5:

```php
// GridView with Bootstrap 5 classes
echo GridView::widget()
    ->tableClass('table', 'table-striped', 'table-hover', 'table-bordered')
    ->filterCellInvalidClass('is-invalid')
    ->filterErrorsContainerAttributes(['class' => 'invalid-feedback']);

// Pagination with Bootstrap 5 classes
echo OffsetPagination::widget()
    ->listAttributes(['class' => 'pagination'])
    ->itemAttributes(['class' => 'page-item'])
    ->linkAttributes(['class' => 'page-link'])
    ->currentItemClass('active')
    ->disabledItemClass('disabled');
```

### Tailwind CSS

Here's how to style your components for Tailwind CSS:

```php
// GridView with Tailwind classes
echo GridView::widget()
    ->tableClass('min-w-full', 'divide-y', 'divide-gray-200')
    ->headerCellAttributes(['class' => 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'])
    ->bodyCellAttributes(['class' => 'px-6 py-4 whitespace-nowrap text-sm text-gray-500']);

// Pagination with Tailwind classes
echo OffsetPagination::widget()
    ->listAttributes(['class' => 'flex items-center justify-between'])
    ->itemAttributes(['class' => 'relative inline-flex items-center px-4 py-2 border text-sm font-medium'])
    ->currentItemClass('bg-blue-50 border-blue-500 text-blue-600')
    ->disabledItemClass('bg-gray-100 text-gray-400');
```

## Best Practices

1. **Consistent Styling**
   - Use consistent class names across your application
   - Create reusable class combinations for common elements

2. **Responsive Design**
   - Use responsive classes for table layouts
   - Consider mobile-first approach when styling

3. **Accessibility**
   - Include proper ARIA attributes
   - Ensure sufficient color contrast
   - Provide visible focus states

4. **Performance**
   - Minimize the use of inline styles
   - Use CSS classes instead of individual attributes when possible
   - Consider using CSS utilities for common patterns

5. **Maintainability**
   - Group related styles together
   - Use semantic class names
   - Document custom styling patterns