<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use InvalidArgumentException;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\A;
use Yiisoft\Html\Tag\I;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\Widget;

use function array_merge;
use function http_build_query;
use function implode;
use function ucfirst;

final class LinkSorter extends Widget
{
    private string $attribute = '';
    private array $attributes = [];
    private int $currentPage = 0;
    private array $directions = [];
    private array $iconAttributes = [];
    private string $iconAsc = '&#x2191;';
    private string $iconAscClass = '';
    private string $iconDesc = '&#x2193;';
    private string $iconDescClass = '';
    private string $label = '';
    private array $linkAttributes = [];
    private array $pageConfig = [];
    private int $pageSize = 0;
    private string $pageName = 'page';
    private string $pageSizeName = 'pagesize';
    private UrlGeneratorInterface|null $urlGenerator = null;
    private array|null $urlArguments = null;
    private array $urlQueryParameters = [];
    private string $urlName = '';

    /**
     * Returns a new instance with the attribute name for link sorting.
     *
     * @param string $value The value label for the link.
     */
    public function attribute(string $value): self
    {
        $new = clone $this;
        $new->attribute = $value;

        return $new;
    }

    /**
     * Returns a new instance with the attributes for the link sorting.
     *
     * @param array $values The attributes for the link sorting.
     */
    public function attributes(array $values): self
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Return a new instance with current page of pagination.
     *
     * @param int $value Current page.
     */
    public function currentPage(int $value): self
    {
        $new = clone $this;
        $new->currentPage = $value;

        return $new;
    }

    /**
     * Returns a new instance with the currently requested sort information.
     *
     * @param array $values The currently requested sort information.
     */
    public function directions(array $values): self
    {
        $new = clone $this;
        $new->directions = $values;

        return $new;
    }

    /**
     * Returns a new instance with the icon text for the ascending sort direction.
     *
     * @param string $value The icon text for the ascending sort direction.
     */
    public function iconAsc(string $value): self
    {
        $new = clone $this;
        $new->iconAsc = $value;

        return $new;
    }

    /**
     * Returns a new instance with the icon text for the descending sort direction.
     *
     * @param string $value The icon text for the descending sort direction.
     */
    public function iconDesc(string $value): self
    {
        $new = clone $this;
        $new->iconDesc = $value;

        return $new;
    }

    /**
     * Returns a new instance with the CSS class for the ascending sort direction.
     *
     * @param string $value The CSS class for the ascending sort direction.
     */
    public function iconAscClass(string $value): self
    {
        $new = clone $this;
        $new->iconAsc = '';
        $new->iconAscClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the CSS class for the descending sort direction.
     *
     * @param string $value The CSS class for the descending sort direction.
     */
    public function iconDescClass(string $value): self
    {
        $new = clone $this;
        $new->iconDesc = '';
        $new->iconDescClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the label for attribute.
     *
     * @param string $value The label for attribute.
     */
    public function label(string $value): self
    {
        $new = clone $this;
        $new->label = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes for a tag `<a>`.
     *
     * @param array $values Attribute values indexed by attribute names.
     */
    public function linkAttributes(array $values): self
    {
        $new = clone $this;
        $new->linkAttributes = $values;

        return $new;
    }

    /**
     * Return a new instance with page config for arguments or query parameters in url.
     *
     * @param array $value The page config for arguments or query parameters in url.
     */
    public function pageConfig(array $value): self
    {
        $new = clone $this;
        $new->pageConfig = $value;

        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page.
     *
     * @param string $value The name of argument or query parameter for page.
     */
    public function pageName(string $value): self
    {
        $new = clone $this;
        $new->pageName = $value;

        return $new;
    }

    /**
     * Return a new instance with page size of pagination.
     *
     * @param int $value The page size of pagination.
     */
    public function pageSize(int $value): self
    {
        $new = clone $this;
        $new->pageSize = $value;

        return $new;
    }

    /**
     * Return a new instance with name of argument or query parameter for page size.
     *
     * @param string $value The name of argument or query parameter for page size.
     */
    public function pageSizeName(string $value): self
    {
        $new = clone $this;
        $new->pageSizeName = $value;

        return $new;
    }

    /**
     * Return a new instance with arguments of the route.
     *
     * @param array $value Arguments of the route.
     */
    public function urlArguments(array $value): self
    {
        $new = clone $this;
        $new->urlArguments = $value;

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
     * Returns a new instance with the name of the route.
     *
     * @param string $value The name of the route.
     */
    public function urlName(string $value): self
    {
        $new = clone $this;
        $new->urlName = $value;

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

    protected function run(): string
    {
        return match (isset($this->attributes[$this->attribute])) {
            true => $this->renderSorterLink(),
            false => '',
        };
    }

    /**
     * Creates the sort variable for the specified attribute.
     *
     * The newly created sort variable can be used to create a URL that will lead to sorting by the specified attribute.
     *
     * @param string $attribute the attribute name.
     *
     * @throws InvalidArgumentException if the specified attribute is not defined.
     */
    private function createSorterParam(string $attribute): string
    {
        $attributes = $this->attributes;
        $directions = $this->directions;
        $direction = 'asc';

        if (isset($attributes['default'])) {
            /** @var string */
            $direction = $attributes['default'];
        }

        if (isset($directions[$attribute])) {
            $direction = $directions[$attribute] === 'desc' ? 'asc' : 'desc';
            unset($directions[$attribute]);
        }

        $directions = array_merge([$attribute => $direction], $directions);
        $sorts = [];

        /** @psalm-var array<string,string> $directions */
        foreach ($directions as $attribute => $direction) {
            $sorts[] = $direction === 'desc' ? '-' . $attribute : $attribute;
        }

        return implode(',', $sorts);
    }

    /**
     * Creates a URL for sorting the data by the specified attribute.
     *
     * This method will consider the current sorting status.
     *
     * For example, if the current page already sorts the data by the specified attribute in ascending order, then the
     * URL created will lead to a page that sorts the data by the specified attribute in descending order.
     *
     * @param string $attribute The attribute name.
     *
     * @throws InvalidArgumentException If the attribute is unknown.
     */
    private function createUrl(string $attribute): string
    {
        if (null === $this->urlGenerator) {
            throw new InvalidArgumentException('UrlGenerator must be configured.');
        }

        $pageConfig = $this->pageConfig;
        $urlQueryParameters = $this->urlQueryParameters;

        if ($pageConfig === []) {
            $pageConfig = [$this->pageName => $this->currentPage, $this->pageSizeName => $this->pageSize];
        }

        if ($this->urlArguments !== null) {
            /** @psalm-var array<string,string> */
            $urlArguments = array_merge($this->urlArguments, $pageConfig);
            $urlArguments['sort'] = $this->createSorterParam($attribute);
        } else {
            $urlArguments = [];
            $urlQueryParameters = array_merge($urlQueryParameters, $pageConfig);
            $urlQueryParameters['sort'] = $this->createSorterParam($attribute);
        }

        return match ($this->urlName !== '') {
            true => $this->urlGenerator->generate($this->urlName, $urlArguments, $urlQueryParameters),
            false => $urlQueryParameters ? '?' . http_build_query($urlQueryParameters) : '',
        };
    }

    private function renderLabel(
        string $label,
        string $icon,
        string $iconClass,
        array $iconAttributes = []
    ): string {
        $html = '';

        if ($iconClass !== '') {
            Html::addCssClass($iconAttributes, $iconClass);
        }

        if ($label !== '') {
            $html = Html::encode($label);
        }

        if ($icon !== '' || $iconClass !== '') {
            $html .= ' ' . I::tag()->addAttributes($iconAttributes)->content($icon)->encode(false)->render();
        }

        return $html;
    }

    /**
     * Generates a hyperlink that links to the sort urlName to sort by the specified attribute.
     *
     * Based on the sort direction, the CSS class of the generated hyperlink will be appended with "asc" or "desc".
     *
     * There is one special attribute `label` which will be used as the label of the hyperlink.
     *
     * If no label is defined, the attribute name will be used.
     *
     * @throws InvalidArgumentException If the attribute is unknown.
     */
    private function renderSorterLink(): string
    {
        $icon = '';
        $iconClass = '';
        $linkAttributes = $this->linkAttributes;
        $sorterClass = '';

        if (isset($this->directions[$this->attribute]) && $this->directions[$this->attribute] === 'desc') {
            $icon = $this->iconDesc;
            $iconClass = $this->iconDescClass;
            $sorterClass = 'desc';
        } elseif (isset($this->directions[$this->attribute])) {
            $icon = $this->iconAsc;
            $iconClass = $this->iconAscClass;
            $sorterClass = 'asc';
        }

        if ($sorterClass !== '') {
            Html::addCssClass($linkAttributes, $sorterClass);
        }

        $linkAttributes['data-sort'] = $this->createSorterParam($this->attribute);
        $label = $this->label !== '' ? $this->label : ucfirst($this->attribute);

        return A::tag()
            ->addAttributes($linkAttributes)
            ->content($this->renderLabel($label, $icon, $iconClass, $this->iconAttributes))
            ->encode(false)
            ->href($this->createUrl($this->attribute))
            ->render();
    }
}
