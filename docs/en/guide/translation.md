# Translation

The DataView package provides built-in internationalization support through the `TranslatorInterface` from
[yiisoft/translator](https://github.com/yiisoft/translator) package.

This allows you to create multilingual data views with minimal effort.

By default, DataView uses the `yii-dataview` translation category.

You can change this using the `translationCategory()` method:

```php
use Yiisoft\Yii\DataView\GridView;

echo GridView::widget(['translationCategory' => 'my-category'])
    ->dataReader($dataReader)
    ->render();
```

If you're using Yii application, you can set the category via params for all widgets at once:

```php
return [
    'yiisoft/yii-dataview' => [
        'translation.category' => BaseListView::DEFAULT_TRANSLATION_CATEGORY,
    ],
];
```

All strings used in widgets are translated by default.
[Several languages are supported](https://github.com/yiisoft/yii-dataview/tree/master/messages).
If you need to customize strings, adjust `messages/{languageCode}/yii-dataview.php`:

```php
<?php

declare(strict_types=1);

return [
    'No results found.' => '...',
    'Page <b>{currentPage}</b> of <b>{totalPages}</b>' => '...',
    'Actions' => '...',
];
```
