<?php

namespace Yiisoft\Yii\DataView\Widget;

use Yiisoft\Data\Reader\Sort;
use Yiisoft\Factory\Exceptions\InvalidConfigException;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * LinkSorter renders a list of sort links for the given sort definition.
 * LinkSorter will generate a hyperlink for every attribute declared in [[sort]].
 * For more details and usage information on LinkSorter, see the [guide article on sorting](guide:output-sorting).
 */
class LinkSorter extends Widget
{
    /**
     * @var \Yiisoft\Data\Reader\Sort|null the sort definition
     */
    private ?Sort $sort = null;
    /**
     * @var array list of the attributes that support sorting. If not set, it will be determined
     *            using [[Sort::attributes]].
     */
    private array $attributes = [];
    /**
     * @var array HTML attributes for the sorter container tag.
     * @see Html::ul() for special attributes.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $options = ['class' => 'sorter'];
    /**
     * @var array HTML attributes for the link in a sorter container tag which are passed to [[Sort::link()]].
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $linkOptions = [];

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
        $attributes = $this->attributes === [] && $this->sort !== null
            ? array_keys($this->sort->getOrder())
            : $this->attributes;
        $links = [];
        foreach ($attributes as $name) {
            // TODO There is need to figure out how to generate links and fix this stub
            $links[] = Html::a($name, sprintf('?sort=%s', $name), $this->linkOptions);
        }

        return Html::ul($links, array_merge($this->options, ['encode' => false]));
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setSort(Sort $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function setLinkOptions(array $linkOptions): self
    {
        $this->linkOptions = $linkOptions;

        return $this;
    }
}
