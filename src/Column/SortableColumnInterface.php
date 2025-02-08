<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * Interface for column renderers that support sorting functionality.
 *
 * This interface defines the contract for column renderers that need to provide
 * sorting capabilities. It allows columns to specify which properties can be sorted
 * and how they map to actual data fields.
 *
 * @template TColumn as ColumnInterface
 */
interface SortableColumnInterface
{
    /**
     * Gets the mapping of sortable properties to their corresponding field names.
     *
     * This method returns an array where:
     * - Keys are logical property names used in the UI and URLs
     * - Values are the actual field names in the data source
     *
     * For example:
     *
     * ```php
     * [
     *     'fullName' => 'last_name', // Sort by 'fullName' will use 'last_name' field
     *     'joinDate' => 'created_at', // Sort by 'joinDate' will use 'created_at' field
     * ]
     * ```
     *
     * @param ColumnInterface $column The column instance being rendered
     *
     * @return array<string, string> The properties that can be sorted by this column
     *
     * @psalm-param TColumn $column
     * @psalm-return array<string, string>
     */
    public function getOrderProperties(ColumnInterface $column): array;
}
