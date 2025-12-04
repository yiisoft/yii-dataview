<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ValuePresenter;

use DateTimeInterface;
use InvalidArgumentException;
use Stringable;
use UnitEnum;

use function gettype;

/**
 * Presents scalars and Stringable objects as strings.
 */
final class SimpleValuePresenter implements ValuePresenterInterface
{
    /**
     * @param string $null Label to use for `null`.
     * @param string $true Label to use for `true`.
     * @param string $false Label to use for `false`.
     * @param string $dateTimeFormat The format to use for `DateTimeInterface` objects.
     */
    public function __construct(
        private readonly string $null = '',
        private readonly string $true = 'True',
        private readonly string $false = 'False',
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function present(mixed $value): string|Stringable
    {
        return match (gettype($value)) {
            'NULL' => $this->null,
            'boolean' => $value ? $this->true : $this->false,
            'string' => $value,
            'integer', 'double' => (string) $value,
            'object' => match (true) {
                $value instanceof Stringable => $value,
                $value instanceof DateTimeInterface => $value->format($this->dateTimeFormat),
                $value instanceof UnitEnum => $value->name,
                default => $this->throwUnsupportedType($value),
            },
            default => $this->throwUnsupportedType($value),
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    private function throwUnsupportedType(mixed $value): never
    {
        throw new InvalidArgumentException('Unsupported value type: ' . get_debug_type($value));
    }
}
