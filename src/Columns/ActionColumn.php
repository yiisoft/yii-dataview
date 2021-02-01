<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Columns;

use Closure;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;

use function array_flip;
use function array_intersect_key;
use function array_merge;
use function call_user_func;
use function is_array;
use function is_callable;
use function preg_match_all;
use function preg_replace_callback;
use function ucfirst;

/**
 * ActionColumn is a column for the {@see GridView} widget that displays buttons for viewing and manipulating the items.
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         '__class' => ActionColumn::class,
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 * For more details and usage information on ActionColumn, see the [guide article on data
 * widgets](guide:output-data-widgets).
 */
class ActionColumn extends Column
{
    protected array $headerOptions = ['class' => 'action-column'];
    private string $template = '{view} {update} {delete}';
    private array $buttons = [];
    private array $visibleButtons = [];
    private array $buttonOptions = [];
    private string $primaryKey = 'id';
    private UrlGeneratorInterface $urlGenerator;
    /** @var callable $urlCreator */
    private $urlCreator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->initDefaultButtons();
    }

    private function initDefaultButtons(): void
    {
        $this->initDefaultButton('view', 'eye-open');
        $this->initDefaultButton('update', 'pencil');
        $this->initDefaultButton(
            'delete',
            'trash',
            [
                'data-confirm' => $this->formatMessage(
                    'Are you sure you want to delete this item?',
                    []
                ),
                'data-method' => 'post',
            ]
        );
    }

    /**
     * Initializes the default button rendering callback for single button.
     *
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     */
    protected function initDefaultButton(string $name, string $iconName, array $additionalOptions = []): void
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = $this->formatMessage('View', []);
                        break;
                    case 'update':
                        $title = $this->formatMessage('Update', []);
                        break;
                    case 'delete':
                        $title = $this->formatMessage('Delete', []);
                        break;
                    default:
                        $title = ucfirst($name);
                }

                $options = array_merge(
                    [
                        'title' => $title,
                        'aria-label' => $title,
                        'data-name' => $name,
                    ],
                    $additionalOptions,
                    $this->buttonOptions
                );

                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);

                return Html::a($icon, $url, $options);
            };
        }
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    /**
     * @param array $buttons rendering callbacks. The array keys are the button names (without curly brackets), and the
     * values are the corresponding button rendering callbacks. The callbacks should use the following signature:
     *
     * ```php
     * function ($url, $model, $key) {
     *     // return the button HTML code
     * }
     * ```
     * where `$url` is the URL that the column creates for the button, `$model` is the model object being rendered for
     * the current row, and `$key` is the key of the model in the data provider array.
     *
     * You can add further conditions to the button, for example only display it, when the model is editable (here
     * assuming you have a status field that indicates that):
     * ```php
     * [
     *     'update' => function ($url, $model, $key) {
     *         return $model->status === 'editable' ? Html::a('Update', $url) : '';
     *     },
     * ],
     * ```
     *
     * @return $this
     */
    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @param array $buttonOptions HTML options to be applied to the default button, see {@see initDefaultButton()}.
     *
     * @return self
     */
    public function buttonOptions(array $buttonOptions): self
    {
        $this->buttonOptions = $buttonOptions;

        return $this;
    }

    /**
     * Indicates which is the primaryKey of the data to be used to generate the url automatically.
     *
     * @param string $primaryKey by default the primaryKey is `id`
     *
     * @return self
     */
    public function primaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Tokens enclosed within curly brackets are treated as controller action IDs (also called *button names* in the
     * context of action column). They will be replaced by the corresponding button rendering callbacks specified in
     * {@see buttons}. For example, the token `{view}` will be replaced by the result of the callback `buttons['view']`.
     * If a callback cannot be found, the token will be replaced with an empty string. As an example, to only have the
     * view, and update button you can add the ActionColumn to your GridView  columns as follows:
     *
     * ```php
     * 'columns' => [
     *     '__class' => ActionColumn::class,
     *     'template()' => ['{view} {update}'],
     * ],
     * ```
     *
     * @param string $template the template used for composing each cell in the action column.
     *
     * @return $this
     *
     * @see buttons
     */
    public function template(string $template): self
    {
        $result = preg_match_all('/{([\w\-\/]+)}/', $template, $matches);

        if ($result > 0 && is_array($matches) && !empty($matches[1])) {
            $this->buttons = array_intersect_key($this->buttons, array_flip($matches[1]));
        }

        $this->template = $template;

        return $this;
    }

    /**
     * @param callable $urlCreator a callback that creates a button URL using the specified model information.
     *
     * The signature of the callback should be the same as that of {@see createUrl()}. It can accept additional
     * parameter, which refers to the column instance itself:
     * ```php
     * function (string $action, mixed $model, mixed $key, integer $index, ActionColumn $this) {
     *     //return string;
     * }
     * ```
     *
     * If this property is not set, button URLs will be created using {@see createUrl()}.
     *
     * @return $this
     */
    public function urlCreator(callable $urlCreator): self
    {
        $this->urlCreator = $urlCreator;

        return $this;
    }

    /**
     * @param array $visibleButtons visibility conditions for each button. The array keys are the button names (without
     * curly brackets), and the values are the boolean true/false or the anonymous function. When the button name is not
     * specified in this array it will be shown by default.
     *
     * The callbacks must use the following signature:
     * ```php
     * function ($model, $key, $index) {
     *     return $model->status === 'editable';
     * }
     * ```
     *
     * Or you can pass a boolean value:
     *
     * ```php
     * [
     *     'update' => true,
     * ],
     * ```
     *
     * @return $this
     */
    public function visibleButtons(array $visibleButtons): self
    {
        $this->visibleButtons = $visibleButtons;

        return $this;
    }

    /**
     * Creates a URL for the given action and model.
     *
     * This method is called for each button and each row.
     *
     * @param string $action the button name (or action ID)
     * @param array $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the current row index
     *
     * @return string the created URL
     */
    public function createUrl(string $action, array $model, $key, int $index): string
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        }

        $key = $model[$this->primaryKey] ?? $key;

        $params = is_array($key) ? $key : ['id' => (string) $key];

        return $this->urlGenerator->generate($action, $params);
    }

    protected function renderDataCellContent(array $model, $key, int $index): string
    {
        return preg_replace_callback(
            '/{([\w\-\/]+)}/',
            function ($matches) use ($model, $key, $index) {
                $name = $matches[1];

                if (isset($this->visibleButtons[$name])) {
                    $isVisible = $this->visibleButtons[$name] instanceof Closure
                        ? ($this->visibleButtons[$name])($model, $key, $index)
                        : $this->visibleButtons[$name];
                } else {
                    $isVisible = true;
                }

                if ($isVisible && isset($this->buttons[$name])) {
                    $url = $this->createUrl($name, $model, $key, $index);

                    return ($this->buttons[$name])($url, $model, $key);
                }

                return '';
            },
            $this->template
        );
    }
}
