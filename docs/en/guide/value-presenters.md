# Value Presenters

Value presenters are responsible for converting data values into string representations for display in data view
widgets. They provide a clean, reusable way to format different types of data consistently across your application.

All value presenters implement the [`ValuePresenterInterface`](../../../src/ValuePresenter/ValuePresenterInterface.php).

## `SimpleValuePresenter`

The built-in `SimpleValuePresenter` handles common data types and provides customizable formatting options:

```php
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;

$presenter = new SimpleValuePresenter(
    null: 'Not Set',          // Label for null values
    true: 'Yes',              // Label for true values
    false: 'No',              // Label for false values
    dateTimeFormat: 'd/m/Y'   // Format for DateTime objects
);
```

### Supported Types

The `SimpleValuePresenter` automatically handles:

- **Null values**: Returns the configured null label (default: empty string)
- **Boolean values**: Returns configured true/false labels (default: 'True'/'False')
- **Strings**: Returns as-is
- **Numbers**: Converts to string
- **DateTime objects**: Formats using the specified format (default: 'Y-m-d H:i:s')
- **Enums**: Returns the enum name property
- **Stringable objects**: Converts to string

If `SimpleValuePresenter` encounters an unsupported type, it throws an `InvalidArgumentException`.

### Examples

```php
$presenter = new SimpleValuePresenter();

echo $presenter->present(null);        // ''
echo $presenter->present(true);        // 'True'
echo $presenter->present(false);       // 'False'
echo $presenter->present('Hello');     // 'Hello'
echo $presenter->present(42);          // '42'
```

## Custom Value Presenters

You can create custom value presenters by implementing the `ValuePresenterInterface`:

```php
use Yiisoft\Yii\DataView\ValuePresenter\ValuePresenterInterface;

final class PricePresenter implements ValuePresenterInterface
{
    public function __construct(
        private readonly string $currency = '$',
        private readonly int $decimals = 2
    ) {}

    public function present(mixed $value): string
    {
        if ($value === null) {
            return 'N/A';
        }

        return $this->currency . number_format((float) $value, $this->decimals);
    }
}
```
