<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Exception;

/**
 * Exception thrown when a filter value is not in the expected format.
 *
 * This exception is thrown by filter factories when they receive a value
 * that cannot be used to create a valid filter. For example,
 *
 * - Invalid date format for date filters
 * - Non-numeric value for numeric filters
 * - Malformed pattern for pattern-based filters
 *
 * @see FilterFactoryInterface The interface whose implementations may throw this exception.
 */
final class IncorrectValueException extends Exception {}
