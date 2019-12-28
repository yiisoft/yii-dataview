<?php

namespace Yiisoft\Yii\DataView\Widget;

use Yiisoft\Data\Reader\Sort;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;

/**
 * LinkSorter renders a list of sort links for the given sort definition.
 * LinkSorter will generate a hyperlink for every attribute declared in [[sort]].
 * For more details and usage information on LinkSorter, see the [guide article on sorting](guide:output-sorting).
 */
class LinkSorter
{
    /**
     * @var \Yiisoft\Data\Reader\Sort the sort definition
     */
    public Sort $sort;
    /**
     * @var array list of the attributes that support sorting. If not set, it will be determined
     *            using [[Sort::attributes]].
     */
    public array $attributes = [];
    /**
     * @var array HTML attributes for the sorter container tag.
     * @see Html::ul() for special attributes.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $options = ['class' => 'sorter'];
    /**
     * @var array HTML attributes for the link in a sorter container tag which are passed to [[Sort::link()]].
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public array $linkOptions = [];

    public static function widget(): self
    {
        return new static();
    }

    /**
     * Initializes the sorter.
     *
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     */
    public function init(): void
    {
        if ($this->sort === null) {
            throw new InvalidConfigException('The "sort" property must be set.');
        }
    }

    public function withSort(Sort $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function withAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Executes the widget.
     * This method renders the sort links.
     *
     * @return string the result of widget execution to be outputted.
     */
    public function run(): string
    {
        return $this->renderSortLinks();
    }

    /**
     * Renders the sort links.
     *
     * @return string the rendering result
     */
    protected function renderSortLinks(): string
    {
        $attributes = empty($this->attributes) ? array_keys($this->sort->getOrder()) : $this->attributes;
        $links = [];
        foreach ($attributes as $name) {
            // TODO There is need to figure out how to generate links and fix this stub
            $links[] = Html::a($name, sprintf('?sort=%s', $name), $this->linkOptions);
        }

        return Html::ul($links, array_merge($this->options, ['encode' => false]));
    }
}
