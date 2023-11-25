<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * `CheckboxColumn` displays a column of checkboxes in a grid view.
 */
final class CheckboxColumn implements ColumnInterface
{
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
