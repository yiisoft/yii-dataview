<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ValuePresenter;

use InvalidArgumentException;
use Stringable;

use function gettype;

final class SimpleValuePresenter implements ValuePresenterInterface
{
    public function __construct(
        private readonly string $null = '',
        private readonly string $true = 'True',
        private readonly string $false = 'False',
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function present(mixed $value): string
    {
        return match (gettype($value)) {
            'NULL' => $this->null,
            'boolean' => $value ? $this->true : $this->false,
            'string' => $value,
            'integer', 'double' => (string) $value,
            'object' => $value instanceof Stringable
                ? (string) $value
                : $this->throwUnsupportedType($value),
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
