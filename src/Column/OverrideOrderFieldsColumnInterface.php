<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * Interface for column renderers that support custom field name mapping for sorting.
 *
 * This interface allows columns to specify different field names for sorting than those used
 * for data display. This is useful when:
 * - The display field name differs from the database field name
 * - Complex sorting involving multiple fields or expressions is needed
 * - Sorting needs to be mapped to a different data source field
 *
 * Example usage:
 * ```php
 * // Map 'fullName' display field to 'last_name' for sorting
 * public function getOverrideOrderFields(ColumnInterface $column): array
 * {
 *     return ['fullName' => 'last_name'];
 * }
 * ```
 */
interface OverrideOrderFieldsColumnInterface
{
    /**
     * Gets the mapping of display field names to actual sort field names.
     *
     * This method allows a column to specify different field names for sorting
     * than those used for displaying data. For example, a column might display
     * a computed "fullName" field but need to sort by "last_name" in the database.
     *
     * @param ColumnInterface $column The column to get sort field mappings for.
     *
     * @return array<string,string> An associative array mapping display field names to sort field names.
     *                             Empty array means no override is needed.
     *                             Keys are display field names, values are sort field names.
     */
    public function getOverrideOrderFields(ColumnInterface $column): array;
}
