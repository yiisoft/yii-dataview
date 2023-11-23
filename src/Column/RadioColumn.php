<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

/**
 * `RadioColumn` displays a column of radio buttons in a grid view.
 */
final class RadioColumn implements ColumnInterface
{
    /**
     * @var callable|null
     */
    private $content;

    public function __construct(
        private ?string $header = null,
        private ?string $footer = null,
        private array $columnAttributes = [],
        private array $headerAttributes = [],
        private array $bodyAttributes = [],
        private array $inputAttributes = [],
        ?string $name = null,
        ?callable $content = null,
        private bool $visible = true,
    ) {
        $this->content = $content;
        if ($name !== null) {
            $this->inputAttributes['name'] = $name;
        }
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function getColumnAttributes(): array
    {
        return $this->columnAttributes;
    }

    public function getHeaderAttributes(): array
    {
        return $this->headerAttributes;
    }

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
    }

    public function getInputAttributes(): array
    {
        return $this->inputAttributes;
    }

    public function getContent(): ?callable
    {
        return $this->content;
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
