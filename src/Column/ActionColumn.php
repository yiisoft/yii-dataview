<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;

use function is_array;

/**
 * `ActionColumn` is a column for the {@see GridView} widget that displays buttons for viewing and manipulating
 * the items.
 */
final class ActionColumn implements ColumnInterface
{

    private Closure|null $urlCreator = null;
    private CurrentRoute $currentRoute;
    private UrlGeneratorInterface|null $urlGenerator = null;

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
        private ?string $header = null,
        private array $buttons = [],
        private array $visibleButtons = [],
        private array $columnAttributes = [],
        private array $bodyAttributes = [],
        private bool $visible = true,
    ) {
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

    public function getHeader(): ?string
    {
        return $this->header;
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

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function getLabel(): string
    {
//        $label = parent::getLabel();
        $label = '';

        return  $label !== '' ? $label : 'Actions';
    }

    /**
     * Return new instance specifying which is the primaryKey of the data to be used to generate the url automatically.
     *
     * @param string $value the primaryKey of the data to be used to generate the url automatically.
     */
    public function primaryKey(string $value): self
    {
        $new = clone $this;
        $new->primaryKey = $value;

        return $new;
    }

    /**
     * Return a new instance with the url creator.
     *
     * @param Closure $value The url creator.
     *
     * The signature of the callback should be the same as that of {@see createUrl()}. It can accept additional
     * parameter, which refers to the column instance itself:
     * ```php
     * function (string $action, array|object $data, mixed $key, int $index) {
     *     //return string;
     * }
     * ```
     *
     * If this property is not set, button URLs will be created using {@see createUrl()}.
     */
    public function urlCreator(Closure $value): self
    {
        $new = clone $this;
        $new->urlCreator = $value;

        return $new;
    }

    /**
     * Return a new instance with URL generator interface for pagination.
     *
     * @param UrlGeneratorInterface $value The URL generator interface for pagination.
     */
    public function urlGenerator(UrlGeneratorInterface $value): self
    {
        $new = clone $this;
        $new->urlGenerator = $value;

        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     */
    public function urlQueryParameters(array $value): self
    {
        $new = clone $this;
        $new->urlQueryParameters = $value;

        return $new;
    }

    /**
     * Return a new instance with config url parameters of the route.
     *
     * @param array $value The config url parameters of the route.
     */
    public function urlParamsConfig(array $value): self
    {
        $new = clone $this;
        $new->urlParamsConfig = $value;

        return $new;
    }

    /**
     * Return new instance whether button is visible or not.
     *
     * @param array $value The visibility conditions for each button. The array keys are the button names (without curly
     * brackets), and the values are the boolean true/false or the anonymous function. When the button name is not
     * specified in this array it will be shown by default.
     *
     * The callbacks must use the following signature:
     *
     * ```php
     * [
     *     visibleButtons => [
     *         update => [
     *             function ($data, $key, int $index) {
     *                 return $data->status === 'editable';
     *             }
     *         ],
     *     ],
     * ]
     * ```
     *
     * Or you can pass a boolean value:
     *
     * ```php
     * [
     *     visibleButtons => [
     *         'update' => true,
     *     ],
     * ],
     * ```
     */
    public function visibleButtons(array $value): self
    {
        $new = clone $this;
        $new->visibleButtons = $value;

        return $new;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     * {@see GridView::dataProvider}.
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        if ($this->getContent() !== null) {
            return '';
//            return parent::renderDataCellContent($data, $key, $index);
        }

        if (empty($this->buttons)) {
            $this->buttons = $this->createDefaultButtons()->buttons;
        }

        return PHP_EOL . preg_replace_callback(
            '/{([\w\-\/]+)}/',
            function (array $matches) use ($data, $key, $index): string {
                $content = '';
                $name = $matches[1];

                if (
                    $this->isVisibleButton($name, $data, $key, $index) &&
                    isset($this->buttons[$name]) &&
                    $this->buttons[$name] instanceof Closure
                ) {
                    $url = $this->createUrl($name, $data, $key, $index);
                    $content = (string) $this->buttons[$name]($url, $data, $key);
                }

                return $content !== '' ? $content . PHP_EOL : '';
            },
            $this->template
        );
    }

    /**
     * Creates a URL for the given action and object. This method is called for each button and each row.
     *
     * @param string $action The button name (or action id).
     * @param array|object $data The data object.
     * @param mixed $key The key associated with the data.
     * @param int $index The current row index.
     */
    private function createUrl(string $action, array|object $data, mixed $key, int $index): string
    {
        if (is_callable($this->urlCreator)) {
            return (string) call_user_func($this->urlCreator, $action, $data, $key, $index, $this);
        }

        if ($this->primaryKey !== '') {
            /** @var mixed */
            $key = $data[$this->primaryKey] ?? $key;
        }

        $currentRouteName = $this->currentRoute->getName() ?? '';

        $route = match ($this->urlName) {
            '' => $currentRouteName . '/' . $action,
            default => $this->urlName . '/' . $action,
        };

        $urlQueryParameters = [];
        $urlParamsConfig = is_array($key) ? $key : [$this->primaryKey => $key];

        $urlParamsConfig = match ($this->urlParamsConfig) {
            [] => $urlParamsConfig,
            default => array_merge($this->urlParamsConfig, $urlParamsConfig),
        };

        if ($this->urlArguments !== null) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($this->urlArguments, $urlParamsConfig);
        } else {
            $urlArguments = [];
            $urlQueryParameters = array_merge($this->urlQueryParameters, $urlParamsConfig);
        }

        return $this->getUrlGenerator()->generate($route, $urlArguments, $urlQueryParameters);
    }

    private function isVisibleButton(string $name, array|object $data, mixed $key, int $index): bool
    {
        $visible = false;

        if ($this->visibleButtons === []) {
            $visible = true;
        }

        if (isset($this->visibleButtons[$name]) && is_bool($this->visibleButtons[$name])) {
            $visible = $this->visibleButtons[$name];
        }

        if (isset($this->visibleButtons[$name]) && $this->visibleButtons[$name] instanceof Closure) {
            /** @var bool */
            $visible = $this->visibleButtons[$name]($data, $key, $index);
        }

        return $visible;
    }

    public function getRenderer(): string
    {
        return ActionColumnRenderer::class;
    }
}
