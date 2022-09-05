<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use Closure;
use InvalidArgumentException;
use Yiisoft\Html\Tag\A;
use Yiisoft\Html\Tag\Span;
use Yiisoft\Router\UrlGeneratorInterface;

use function is_array;

/**
 * ActionColumn is a column for the {@see GridView} widget that displays buttons for viewing and manipulating the items.
 */
final class ActionColumn extends Column
{
    private array $buttons = [];
    private string $primaryKey = 'id';
    private string $template = '{view}{update}{delete}';
    private array $urlArguments = [];
    private Closure|null $urlCreator = null;
    private bool $urlEnabledArguments = true;
    private UrlGeneratorInterface|null $urlGenerator = null;
    private array $urlParamsConfig = [];
    private string $urlName = '';
    private array $urlQueryParameters = [];
    private array $visibleButtons = [];

    /**
     * Return new instance with the buttons array.
     *
     * @param array $value button rendering callbacks. The array keys are the button names (without curly brackets),
     * and the values are the corresponding button rendering callbacks. The callbacks should use the following
     * signature:
     *
     * ```php
     * [
     *     buttons => [
     *         'action' => function (string $url, $data, int $key) {
     *             // return the button HTML code
     *         }
     *     ],
     * ]
     * ```
     *
     * where `$url` is the URL that the column creates for the button, `$data` is the data object being rendered
     * for the current row, and `$key` is the key of the data in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the data is editable (here
     * assuming you have a status field that indicates that):
     *
     * ```php
     * [
     *     buttons = [
     *         'update' => function (string $url, $data, $key) {
     *             return $data->status === 'editable' ? Html::a('Update', $url) : '';
     *         },
     *     ],
     * ],
     * ```
     *
     * @return static
     */
    public function buttons(array $value): static
    {
        $new = clone $this;
        $new->buttons = $value;

        return $new;
    }

    /**
     * Initializes the default button rendering callback for single button.
     *
     * @param string $name The button names as it's written in template.
     * @param string $icon The icon name.
     * @param array $attributes The HTML attributes in terms of name-value pairs.
     *
     * @return static
     */
    public function createDefaultButtons(): static
    {
        /** @psalm-var array<string,Closure> */
        $defaultButtons = [
            'view' => static fn (string $url): string => A::tag()
                ->addAttributes(
                    [
                        'name' => 'view',
                        'role' => 'button',
                        'style' => 'text-decoration: none!important;',
                        'title' => 'View',
                    ],
                )
                ->content(Span::tag()->content('&#128270;')->encode(false))
                ->href($url)
                ->render(),
            'update' => static fn (string $url): string => A::tag()
                ->addAttributes(
                    [
                        'name' => 'update',
                        'role' => 'button',
                        'style' => 'text-decoration: none!important;',
                        'title' => 'Update',
                    ],
                )
                ->content(Span::tag()->content('&#9998;')->encode(false))
                ->href($url)
                ->render(),
            'delete' => static fn (string $url): string => A::tag()
                ->addAttributes(
                    [
                        'name' => 'delete',
                        'role' => 'button',
                        'style' => 'text-decoration: none!important;',
                        'title' => 'Delete',
                    ],
                )
                ->content(Span::tag()->content('&#10060;')->encode(false))
                ->href($url)
                ->render(),
        ];

        $new = clone $this;

        foreach ($defaultButtons as $name => $button) {
            $new->buttons[$name] = $button;
        }

        return $new;
    }

    public function getLabel(): string
    {
        $label = parent::getLabel();

        return  $label !== '' ? $label : 'Actions';
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        if ($this->urlGenerator === null) {
            throw new InvalidArgumentException('Url generator is not set.');
        }

        return $this->urlGenerator;
    }

    /**
     * Return new instance specifying which is the primaryKey of the data to be used to generate the url automatically.
     *
     * @param string $value the primaryKey of the data to be used to generate the url automatically.
     *
     * @return static
     */
    public function primaryKey(string $value): static
    {
        $new = clone $this;
        $new->primaryKey = $value;

        return $new;
    }

    /**
     * Return new instance with the template set.
     *
     * @param string $value The template used for composing each cell in the action column. Tokens enclosed within curly
     * brackets are treated as controller action IDs (also called *button names* in the context of action column).
     *
     * They will be replaced by the corresponding button rendering callbacks specified in {@see buttons}. For example,
     * the token `{view}` will be replaced by the result of the callback `buttons['view']`. If a callback cannot be
     * found, the token will be replaced with an empty string.
     *
     * As an example, to only have the view, and update button you can add the ActionColumn to your GridView columns as
     * follows:
     *
     * ```php
     * [
     *     'class' => ActionColumn::class,
     *     'template()' => ['{view} {update} {delete}'],
     * ],
     * ```
     *
     * @return static
     *
     * {@see buttons}
     */
    public function template(string $value): static
    {
        $new = clone $this;

        $result = preg_match_all('/{([\w\-\/]+)}/', $value, $matches);

        if ($result > 0 && !empty($matches[1])) {
            $new->buttons = array_intersect_key($new->buttons, array_flip($matches[1]));
        }

        $new->template = $value;

        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $value Arguments of the route.
     *
     * @return static
     */
    public function urlArguments(array $value): static
    {
        $new = clone $this;
        $new->urlArguments = $value;

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
     *
     * @return $this
     */
    public function urlCreator(Closure $value): static
    {
        $new = clone $this;
        $new->urlCreator = $value;

        return $new;
    }

    /**
     * Return a new instance with enabled arguments of the route.
     *
     * @param bool $value Enabled arguments of the route.
     *
     * @return static
     */
    public function urlEnabledArguments(bool $value): static
    {
        $new = clone $this;
        $new->urlEnabledArguments = $value;

        return $new;
    }

    /**
     * Return a new instance with url generator interface for pagination.
     *
     * @param UrlGeneratorInterface $value The url generator interface for pagination.
     *
     * @return static
     */
    public function urlGenerator(UrlGeneratorInterface $value): static
    {
        $new = clone $this;
        $new->urlGenerator = $value;

        return $new;
    }

    /**
     * Returns a new instance with the name of the route.
     *
     * @param string $value The name of the route.
     *
     * @return static
     */
    public function urlName(string $value): static
    {
        $new = clone $this;
        $new->urlName = $value;

        return $new;
    }

    /**
     * Return a new instance with query parameters of the route.
     *
     * @param array $value The query parameters of the route.
     *
     * @return static
     */
    public function urlQueryParameters(array $value): static
    {
        $new = clone $this;
        $new->urlQueryParameters = $value;

        return $new;
    }

    /**
     * Return a new instance with config url parameters of the route.
     *
     * @param array $value The config url parameters of the route.
     *
     * @return static
     */
    public function urlParamsConfig(array $value): static
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
     *
     * @return static
     */
    public function visibleButtons(array $value): static
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
     *
     * @return string
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        if ($this->getContent() !== null) {
            return parent::renderDataCellContent($data, $key, $index);
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
     *
     * @return string
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

        $route = $this->urlName !== '' ? $this->urlName . '/' . $action : $action;
        $urlArguments = [];
        $urlQueryParameters = [];
        $urlParamsConfig = is_array($key) ? $key : [$this->primaryKey => $key];

        $urlParamsConfig = match ($this->urlParamsConfig) {
            [] => $urlParamsConfig,
            default => array_merge($this->urlParamsConfig, $urlParamsConfig),
        };

        if ($this->urlEnabledArguments) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($this->urlArguments, $urlParamsConfig);
        } else {
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
}