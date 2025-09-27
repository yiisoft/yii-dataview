<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

use Closure;

/**
 * `RadioColumn` displays a column of radio buttons in a grid view.
 *
 * This column type is typically used when you need to allow users to select a single row from the grid.
 * Each radio button in the column shares the same name attribute, ensuring only one can be selected at a time.
 */
final class RadioColumn implements ColumnInterface
{
    /**
     * Creates a new `RadioColumn` instance.
     *
     * @param string|null $header The header cell content. If `null`, no header will be rendered.
     * @param string|null $footer The footer cell content. If `null`, no footer will be rendered.
     * @param array $columnAttributes HTML attributes for all column cells.
     * @param array $headerAttributes HTML attributes for the header cell.
     * @param array $bodyAttributes HTML attributes for the body cells.
     * @param array $inputAttributes HTML attributes for the radio input elements.
     * The `name` attribute will be set from the `$name` parameter if provided.
     * @param string|null $name The name attribute for all radio inputs in this column.
     * All radio buttons will share this name, ensuring single selection.
     * @param Closure|null $content Optional callback to generate the radio button value.
     * Signature: `function($model): string`.
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
        public ?Closure $content = null,
        private readonly bool $visible = true,
    ) {
        if ($name !== null) {
            $this->inputAttributes['name'] = $name;
        }
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getRenderer(): string
    {
        return RadioColumnRenderer::class;
    }
}
