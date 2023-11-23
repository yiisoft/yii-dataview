<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;

/**
 * `ActionColumn` is a column for the {@see GridView} widget that displays buttons for viewing and manipulating
 * the items.
 */
final class ActionColumn implements ColumnInterface
{
    /**
     * @var callable|null
     */
    private $urlCreator;

    /**
     * @psalm-param array<string,Closure> $buttons
     */
    public function __construct(
        private string $primaryKey = 'id',
        private string $template = "{view}\n{update}\n{delete}",
        private ?string $routeName = null,
        private array $urlParamsConfig = [],
        private ?array $urlArguments = null,
        private array $urlQueryParameters = [],
        ?callable $urlCreator = null,
        private ?string $header = null,
        private ?string $footer = null,
        private mixed $content = null,
        private array $buttons = [],
        private array $visibleButtons = [],
        private array $columnAttributes = [],
        private array $headerAttributes = [],
        private array $bodyAttributes = [],
        private array $footerAttributes = [],
        private bool $visible = true,
    ) {
        $this->urlCreator = $urlCreator;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getUrlParamsConfig(): array
    {
        return $this->urlParamsConfig;
    }

    public function getUrlArguments(): ?array
    {
        return $this->urlArguments;
    }

    public function getUrlQueryParameters(): array
    {
        return $this->urlQueryParameters;
    }

    public function getUrlCreator(): ?callable
    {
        return $this->urlCreator;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * @psalm-return array<string,Closure>
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function getVisibleButtons(): array
    {
        return $this->visibleButtons;
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

    public function getFooterAttributes(): array
    {
        return $this->footerAttributes;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getRenderer(): string
    {
        return ActionColumnRenderer::class;
    }
}
