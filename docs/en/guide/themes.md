# Theming

The data view package provides extensive theming capabilities through HTML attributes and CSS classes.
You can customize the appearance of all widgets and individual elements.

Themes are part of [yiisoft/widget](https://github.com/yiisoft/widget) and are defined in application parameters which
usually could be found in `config/web/params.php`:

```php
return [
    // ...
    'yiisoft/widget' => [
        'defaultTheme' => 'bootstrap5',
    ],
];
```

Themes are available via `widgets-themes` configuration group.
To define a theme, ensure that the following is in your `composer.json`:

```json
"config-plugin": {
  // ...
  "widgets-themes": "widgets-themes.php",
}
```

Then you can define extra themes in `config/web/widgets-themes.php`:

```php
return [
    'mytheme' => [
        // keys are widget classes
        GridView::class => [
            // values are methods and their arguments
            'sortableHeaderAscPrepend()' => ['↑'],
            'sortableHeaderDescPrepend()' => ['↓'],
        ],
    ],
];
```
