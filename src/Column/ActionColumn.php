<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Yiisoft\Yii\DataView\Column\Base\DataContext;

/**
 * `ActionColumn` is a column for the {@see GridView} widget that displays buttons for viewing and manipulating
 * the items.
 *
 * @psalm-type UrlCreator = callable(string,DataContext):string
 */
final class ActionColumn implements ColumnInterface
{
    /**
     * @var UrlCreator|null
     */
    private $urlCreator;

    /**
     * @param ?string $primaryKey The primary key of the data to be used to generate an URL automatically.
     * @param ?callable $urlCreator A callback that creates a button URL using the specified data information.
     *
     * @psalm-param UrlCreator|null $urlCreator
     * @psalm-param array<string,Closure> $buttons
     * @psalm-param array<string,bool|Closure>|null $visibleButtons
     */
    public function __construct(
        public readonly ?string $primaryKey = null,
        public readonly ?string $template = null,
        public readonly mixed $urlConfig = null,
        ?callable $urlCreator = null,
        public readonly ?string $header = null,
        public readonly ?string $footer = null,
        public readonly mixed $content = null,
        public readonly array $buttons = [],
        public readonly ?array $visibleButtons = null,
        public readonly array $columnAttributes = [],
        public readonly array $headerAttributes = [],
        public readonly array $bodyAttributes = [],
        public readonly array $footerAttributes = [],
        private readonly bool $visible = true,
    ) {
        $this->urlCreator = $urlCreator;
    }

    /**
     * @psalm-return UrlCreator|null
     */
    public function getUrlCreator(): ?callable
    {
        return $this->urlCreator;
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
