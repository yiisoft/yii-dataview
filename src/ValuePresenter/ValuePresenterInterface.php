<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ValuePresenter;

use Stringable;

/**
 * A value presenter is responsible for presenting a given value as a string.
 */
interface ValuePresenterInterface
{
    /**
     * Formats the given value into a string.
     *
     * @param mixed $value The value to present.
     * @return string|Stringable The presented value.
     */
    public function present(mixed $value): string|Stringable;
}
