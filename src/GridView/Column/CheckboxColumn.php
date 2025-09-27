<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\GridView\Column;

use Closure;
use Stringable;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

/**
 * `CheckboxColumn` displays a column of checkboxes in a grid view.
 *
 * @psalm-type TContentClosure = Closure(Checkbox, DataContext): (string|Stringable)
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
     * the signature: `function(Checkbox $input, DataContext $context): string|Stringable`.
     * @param bool $visible Whether the column is visible.
     *
     * @psalm-param TContentClosure|null $content
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

    public function getRenderer(): string
    {
        return CheckboxColumnRenderer::class;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }
}
