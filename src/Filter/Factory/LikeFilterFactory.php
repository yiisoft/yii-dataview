<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Factory;

use Yiisoft\Data\Reader\Filter\Like;

/**
 * Factory for creating pattern matching (LIKE) filters.
 *
 * This factory creates filters that perform pattern matching using SQL LIKE operator
 * semantics. It supports both case-sensitive and case-insensitive matching, which
 * can be configured during factory instantiation.
 *
 * Pattern matching follows SQL LIKE operator rules:
 * - % matches any sequence of characters
 * - _ matches any single character
 */
final class LikeFilterFactory implements FilterFactoryInterface
{
    /**
     * @param bool|null $caseSensitive Whether the pattern matching should be case-sensitive.
     * - `true` for case-sensitive matching
     * - `false` for case-insensitive matching
     * - `null` to use the data reader's default behavior
     */
    public function __construct(
        private readonly ?bool $caseSensitive = null,
    ) {
    }

    public function create(string $property, string $value): ?Like
    {
        return new Like($property, $value, $this->caseSensitive);
    }
}
