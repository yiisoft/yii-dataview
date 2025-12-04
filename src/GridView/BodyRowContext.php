<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView;

/**
 * `BodyRowContext` provides contextual information for rendering a table body row in data views.
 */
final class BodyRowContext
{
    /**
     * Creates a new instance of body row context.
     *
     * @param array|object $data The data for the current row. Can be either an array or an object
     * containing the row's data.
     * @param int|string $key The unique identifier for the row. This can be either an integer
     * or a string that uniquely identifies the row in the dataset.
     * @param int $index The zero-based index of the row in the dataset. This represents
     * the row's position in the current page of results.
     */
    public function __construct(
        public readonly array|object $data,
        public readonly int|string $key,
        public readonly int $index,
    ) {}
}
