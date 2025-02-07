# Translation

The Yii DataView component provides built-in internationalization support through the `TranslatorInterface`. This allows you to create multilingual data views with minimal effort.

## Basic Configuration

### Setting up the Translator

You can provide a translator instance when creating any DataView widget:

```php
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\GridView;

// Create a translator instance
$translator = new Translator(/* ... */);

// Use translator in GridView
echo GridView::widget()
    ->translator($translator)
    ->translationCategory('dataview')
    ->dataReader($dataReader)
    ->render();
```

If no translator is provided, a default translator will be created automatically using either `IntlMessageFormatter` (if the PHP `intl` extension is available) or `SimpleMessageFormatter`.

### Translation Category

By default, DataView uses the `dataview` translation category. You can change this using the `translationCategory()` method:

```php
echo GridView::widget()
    ->translationCategory('my-category')
    ->dataReader($dataReader)
    ->render();
```

## Translatable Elements

### Empty Text

The text shown when no data is available can be translated:

```php
echo GridView::widget()
    ->emptyText('No records found') // Will be translated using current category
    ->dataReader($dataReader)
    ->render();
```

### Summary Text

The summary section supports translation tokens:

```php
echo GridView::widget()
    ->summaryTemplate('Showing {begin}-{end} of {totalCount} items.') // Will be translated
    ->dataReader($dataReader)
    ->render();
```

### Column Headers and Content

In GridView, column headers and content can be translated:

```php
use Yiisoft\Yii\DataView\Column\DataColumn;

echo GridView::widget()
    ->columns(
        (new DataColumn())
            ->header('Status') // Will be translated
            ->content(fn ($model) => $model->status) // Raw content can be translated in the callback
    )
    ->dataReader($dataReader)
    ->render();
```

### Pagination Labels

Both offset and keyset pagination support translation of navigation labels:

```php
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

echo OffsetPagination::widget()
    ->labelFirst('First Page')    // Will be translated
    ->labelLast('Last Page')      // Will be translated
    ->labelPrevious('Previous')   // Will be translated
    ->labelNext('Next')           // Will be translated
    ->render();
```

```php
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;

echo KeysetPagination::widget()
    ->labelPrevious('Previous')   // Will be translated
    ->labelNext('Next')           // Will be translated
    ->render();
```

## Message Format

### Simple Format

When using `SimpleMessageFormatter`, translations are straightforward replacements:

```php
// Message source
return [
    'dataview' => [
        'No records found' => 'Keine Einträge gefunden',
        'Status' => 'Status',
        'Previous' => 'Vorherige',
        'Next' => 'Nächste',
    ],
];
```

### Intl Format

When the PHP `intl` extension is available, you can use more advanced formatting with `IntlMessageFormatter`:

```php
// Message source
return [
    'dataview' => [
        'Showing {begin}-{end} of {totalCount} items.' => 'Zeige {begin}-{end} von {totalCount} Einträgen.',
        'Page {page} of {totalPages}' => 'Seite {page} von {totalPages}',
    ],
];
```

## Best Practices

1. **Consistent Categories**
   - Use a consistent translation category across your application
   - Consider using subcategories for specific components (e.g., 'dataview.grid', 'dataview.list')

2. **Message Organization**
   - Keep translations organized by functional area
   - Use clear, descriptive message IDs
   - Document any required parameters in message templates

3. **Fallback Handling**
   - Always provide default English messages
   - Consider using fallback languages for incomplete translations

4. **Context and Placeholders**
   - Provide context information in translation files
   - Document the meaning of placeholders
   - Use meaningful placeholder names

5. **Performance**
   - Cache compiled message patterns when using IntlMessageFormatter
   - Load only the required translation categories

## Example Translation File

Here's an example of a complete translation file for German:

```php
return [
    'dataview' => [
        // Empty text
        'No records found' => 'Keine Einträge gefunden',
        
        // Summary
        'Showing {begin}-{end} of {totalCount} items.' => 'Zeige {begin}-{end} von {totalCount} Einträgen.',
        
        // Pagination
        'First Page' => 'Erste Seite',
        'Last Page' => 'Letzte Seite',
        'Previous' => 'Vorherige',
        'Next' => 'Nächste',
        'Page {page} of {totalPages}' => 'Seite {page} von {totalPages}',
        
        // Common column headers
        'ID' => 'ID',
        'Name' => 'Name',
        'Status' => 'Status',
        'Created At' => 'Erstellt am',
        'Updated At' => 'Aktualisiert am',
        'Actions' => 'Aktionen',
        
        // Status values
        'Active' => 'Aktiv',
        'Inactive' => 'Inaktiv',
        'Pending' => 'Ausstehend',
        
        // Actions
        'View' => 'Ansehen',
        'Update' => 'Bearbeiten',
        'Delete' => 'Löschen',
    ],
];