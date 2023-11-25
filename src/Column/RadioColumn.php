<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * `RadioColumn` displays a column of radio buttons in a grid view.
 */
final class RadioColumn implements ColumnInterface
{
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
