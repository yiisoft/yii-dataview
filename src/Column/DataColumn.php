<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Yiisoft\Validator\RuleInterface;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Filter\Factory\FilterFactoryInterface;
use Yiisoft\Yii\DataView\Filter\Widget\FilterWidget;

/**
 * DataColumn is the default column type for the {@see GridView} widget.
 *
 * A simple data column definition refers to an attribute in the data of the GridView's data provider.
 * By setting {@see value} and {@see label}, the label and cell content can be customized.
 *
 * A data column differentiates between the {@see getDataCellValue|data cell value} and the
 * {@see renderDataCellContent|data cell content}. The cell value is an unformatted value that may be used for
 * calculation, while the actual cell content is a {@see format|formatted} version of that value which may contain HTML
 * markup.
 *
 * @psalm-type FilterEmptyCallable = callable(mixed $value): bool
 * @psalm-type BodyAttributesCallable = callable(array|object,DataContext): array
 */
final class DataColumn implements ColumnInterface
{
    /**
     * Function to determine if a filter value should be considered empty.
     *
     * This property can be:
     * - `null`: Uses default empty value checking (empty() function)
     * - `true`: Always considers the value empty (disables filtering)
     * - `false`: Always considers the value non-empty (enables filtering)
     * - `callable`: Custom function to determine emptiness with signature:
     *   ```php
     *   function (mixed $value): bool {
     *       // Return true if value should be considered empty
     *       return $value === '' || $value === null;
     *   }
     *   ```
     *
     * @var bool|callable|null
     * @psalm-var bool|FilterEmptyCallable|null
     */
    public readonly mixed $filterEmpty;

    /**
     * Creates a new DataColumn instance.
     *
     * ```php
     * // Basic usage
     * $column = new DataColumn(
     *     property: 'username',
     *     header: 'User',
     *     withSorting: true
     * );
     *
     * // With filter and validation
     * $column = new DataColumn(
     *     property: 'email',
     *     filter: new TextFilterWidget(),
     *     filterValidation: [new EmailValidator()]
     * );
     *
     * // With custom content
     * $column = new DataColumn(
     *     content: fn($model) => Html::encode($model->fullName)
     * );
     *
     * // With custom empty value checking
     * $column = new DataColumn(
     *     property: 'status',
     *     filterEmpty: fn($value) => $value === 0 || $value === null,
     * );
     * ```
     *
     * @param string|null $property The property name of the data model to be displayed in this column.
     * @param string|null $field The field name to be used in sorting and filtering.
     * @param string|null $header The header cell content.
     * @param bool $encodeHeader Whether to HTML-encode the header cell content.
     * @param string|null $footer The footer cell content.
     * @param array $columnAttributes HTML attributes for all column cells.
     * @param array $headerAttributes HTML attributes for the header cell.
     * @param array|callable $bodyAttributes HTML attributes for the body cells. Can be a callable that returns attributes.
     * The callable signature is: `function(array|object $data, DataContext $context): array`.
     * @param bool $withSorting Whether this column is sortable.
     * @param mixed $content Custom content for data cells. Can be a callable with signature:
     * `function(array|object $data, DataContext $context): string|Stringable`.
     * @param string|null $dateTimeFormat Format string for datetime values (e.g., 'Y-m-d H:i:s').
     * @param bool|array|FilterWidget $filter Filter configuration. Can be:
     * - false (disabled)
     * - array (filter options)
     * - FilterWidget instance (custom filter widget)
     * @param string|FilterFactoryInterface|null $filterFactory Factory for creating filter widgets.
     * @param array|RuleInterface|null $filterValidation Validation rules for filter values.
     * Can be a single rule or array of rules.
     * @param bool|callable|null $filterEmpty Function to determine if a filter value is empty. Can be:
     * - `null`: Uses default empty value checking (empty() function)
     * - `true`: Always considers the value empty (disables filtering)
     * - `false`: Always considers the value non-empty (enables filtering)
     * - `callable`: Custom function to determine emptiness with signature:
     *   ```php
     *   function (mixed $value): bool {
     *       // Return true if value should be considered empty
     *       return $value === '' || $value === null;
     *   }
     *   ```
     * @param bool $visible Whether the column is visible.
     * @param string|null $columnClass Additional CSS class for all column cells.
     * @param string|null $headerClass Additional CSS class for the header cell.
     * @param string|null $bodyClass Additional CSS class for the body cells.
     *
     * @psalm-param array|BodyAttributesCallable $bodyAttributes
     * @psalm-param bool|array<array-key,string|array<array-key,string>>|FilterWidget $filter
     * @psalm-param RuleInterface[]|RuleInterface|null $filterValidation
     * @psalm-param bool|FilterEmptyCallable|null $filterEmpty
     */
    public function __construct(
        public readonly ?string $property = null,
        public readonly ?string $field = null,
        public readonly ?string $header = null,
        public readonly bool $encodeHeader = true,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly mixed $bodyAttributes = [],
        public readonly bool $withSorting = true,
        public readonly mixed $content = null,
        public readonly ?string $dateTimeFormat = null,
        public readonly bool|array|FilterWidget $filter = false,
        public readonly string|FilterFactoryInterface|null $filterFactory = null,
        public readonly array|RuleInterface|null $filterValidation = null,
        bool|callable|null $filterEmpty = null,
        private readonly bool $visible = true,
        public readonly ?string $columnClass = null,
        public readonly ?string $headerClass = null,
        public readonly ?string $bodyClass = null,
    ) {
        $this->filterEmpty = $filterEmpty;
    }

    /**
     * Check if the column is visible.
     *
     * @return bool Whether the column should be rendered.
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Get the renderer class for this column.
     *
     * @return string The fully qualified class name of the renderer.
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string
    {
        return DataColumnRenderer::class;
    }
}
