<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * `CheckboxColumn` displays a column of checkboxes in a grid view.
 */
final class CheckboxColumn implements ColumnInterface
{
    /**
     * @param string|null $header The header cell content.
     * @param string|null $footer The footer cell content.
     * @param array $columnAttributes HTML attributes for the column cells.
     * @param array $headerAttributes HTML attributes for the header cell.
     * @param array $bodyAttributes HTML attributes for the body cells.
     * @param array $inputAttributes HTML attributes for the checkbox input elements.
     * @param string|null $name The name attribute of the checkboxes. If specified, it will be assigned
     * to the "name" attribute of the checkbox input.
     * @param bool $multiple Whether to allow multiple selection. If true, checkbox names will be
     * an array like `name[]`; if false, checkbox names will be single strings.
     * @param Closure|null $content Closure for generating custom checkbox content. If set, it should have
     * the signature: `function(array|object $data, DataContext $context): string`.
     * @param bool $visible Whether the column is visible.
     */
    public function __construct(
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $bodyAttributes = [],
        public array $inputAttributes = [],
        ?string $name = null,
        public readonly bool $multiple = true,
        public ?Closure $content = null,
        private readonly bool $visible = true,
    ) {
        if ($name !== null) {
            $this->inputAttributes['name'] = $name;
        }
    }

    /**
     * Get the renderer class for this column.
     *
     * @return string The fully qualified class name of the renderer.
     * @psalm-return class-string<ColumnRendererInterface>
     */
    public function getRenderer(): string
    {
        return CheckboxColumnRenderer::class;
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
}
