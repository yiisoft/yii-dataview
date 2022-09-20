<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use InvalidArgumentException;
use ReflectionException;
use Stringable;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\CustomTag;
use Yiisoft\Html\Tag\I;
use Yiisoft\Html\Tag\Span;
use Yiisoft\Widget\Widget;

use function array_merge;
use function count;
use function implode;

/**
 * Menu displays a multi-level menu using nested HTML lists.
 *
 * The {@see Menu::items()} method specifies the possible items in the menu.
 * A menu item can contain sub-items which specify the sub-menu under that menu item.
 *
 * Menu checks the current route and request parameters to toggle certain menu items with active state.
 *
 * Note that Menu only renders the HTML tags about the menu. It does not do any styling.
 * You are responsible to provide CSS styles to make it look like a real menu.
 *
 * The following example shows how to use Menu:
 *
 * ```php
 * <?= Menu::Widget()
 *     ->items([
 *         ['label' => 'Login', 'link' => 'site/login', 'visible' => true],
 *     ]);
 * ?>
 * ```
 */
final class Menu extends Widget
{
    private array $afterAttributes = [];
    private string $afterContent = '';
    private string $afterTag = 'span';
    private string $activeClass = 'active';
    private bool $activateItems = true;
    private array $attributes = [];
    private array $beforeAttributes = [];
    private string $beforeContent = '';
    private string $beforeTag = 'span';
    private bool $container = true;
    private string $currentPath = '';
    private string $disabledClass = 'disabled';
    private string $firstItemClass = '';
    private array $iconContainerAttributes = [];
    private array $items = [];
    private bool $itemsContainer = true;
    private array $itemsContainerAttributes = [];
    private string $itemsTag = 'li';
    private string $labelTemplate = '{label}';
    private string $lastItemClass = '';
    private array $linkAttributes = [];
    private string $linkClass = '';
    private string $linkTag = 'a';
    private string $tagName = 'ul';

    /**
     * Returns a new instance with the specified after container attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return self
     */
    public function afterAttributes(array $values): self
    {
        $new = clone $this;
        $new->afterAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified after container class.
     *
     * @param string $value The class name.
     *
     * @return self
     */
    public function afterClass(string $value): self
    {
        $new = clone $this;
        Html::addCssClass($new->afterAttributes, $value);

        return $new;
    }

    /**
     * Returns a new instance with the specified after content.
     *
     * @param string|Stringable $content The content.
     *
     * @return self
     */
    public function afterContent(string|Stringable $content): self
    {
        $new = clone $this;
        $new->afterContent = (string) $content;

        return $new;
    }

    /**
     * Returns a new instance with the specified after container tag.
     *
     * @param string $value The after container tag.
     *
     * @return self
     */
    public function afterTag(string $value): self
    {
        $new = clone $this;
        $new->afterTag = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified active CSS class.
     *
     * @param string $value The CSS class to be appended to the active menu item.
     *
     * @return self
     */
    public function activeClass(string $value): self
    {
        $new = clone $this;
        $new->activeClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the HTML attributes. The following special options are recognized.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return static
     */
    public function attributes(array $values): static
    {
        $new = clone $this;
        $new->attributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified before container attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return self
     */
    public function beforeAttributes(array $values): self
    {
        $new = clone $this;
        $new->beforeAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified before container class.
     *
     * @param string $value The before container class.
     *
     * @return self
     */
    public function beforeClass(string $value): self
    {
        $new = clone $this;
        Html::addCssClass($new->beforeAttributes, $value);

        return $new;
    }

    /**
     * Returns a new instance with the specified before content.
     *
     * @param string|Stringable $value The content.
     *
     * @return self
     */
    public function beforeContent(string|Stringable $value): self
    {
        $new = clone $this;
        $new->beforeContent = (string) $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified before container tag.
     *
     * @param string $value The before container tag.
     *
     * @return self
     */
    public function beforeTag(string $value): self
    {
        $new = clone $this;
        $new->beforeTag = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified the class `menu` widget.
     *
     * @param string $value The class `menu` widget.
     *
     * @return self
     */
    public function class(string $value): self
    {
        $new = clone $this;
        Html::addCssClass($new->attributes, $value);

        return $new;
    }

    /**
     * Returns a new instance with the specified enable or disable the container widget.
     *
     * @param bool $value The container widget enable or disable, for default is `true`.
     *
     * @return self
     */
    public function container(bool $value): self
    {
        $new = clone $this;
        $new->container = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified the current path.
     *
     * @param string $value The current path.
     *
     * @return self
     */
    public function currentPath(string $value): self
    {
        $new = clone $this;
        $new->currentPath = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified disabled CSS class.
     *
     * @param string $value The CSS class to be appended to the disabled menu item.
     *
     * @return self
     */
    public function disabledClass(string $value): self
    {
        $new = clone $this;
        $new->disabledClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified first item CSS class.
     *
     * @param string $value The CSS class that will be assigned to the first item in the main menu or each submenu.
     *
     * @return self
     */
    public function firstItemClass(string $value): self
    {
        $new = clone $this;
        $new->firstItemClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified icon container attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return self
     */
    public function iconContainerAttributes(array $values): self
    {
        $new = clone $this;
        $new->iconContainerAttributes = $values;

        return $new;
    }

    /**
     * Return new instance with the list of items in the nav widget. Each array element represents a single menu item
     * which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - active: bool, whether the item should be on active state or not.
     * - disabled: bool, whether the item should be on disabled state or not. For default `disabled` is false.
     * - encodeLabel: bool, whether the label should be HTML encoded or not. For default `encodeLabel` is true.
     * - items: array, optional, the item's submenu items. The structure is the same as for `items` option.
     * - itemsContainerAttributes: array, optional, the HTML attributes for the item's submenu container.
     * - link: string, the item's href. Defaults to "#". For default `link` is "#".
     * - linkAttributes: array, the HTML attributes of the item's link. For default `linkAttributes` is `[]`.
     * - icon: string, the item's icon. For default is ``.
     * - iconAttributes: array, the HTML attributes of the item's icon. For default `iconAttributes` is `[]`.
     * - iconClass: string, the item's icon CSS class. For default is ``.
     * - liAttributes: array, optional, the HTML attributes of the item container.
     * - visible: bool, optional, whether this menu item is visible. Defaults to true.
     * - dropdown: array, optional, the configuration array for creating dropdown submenu items. The array structure is
     *   the same as the parent item configuration array.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     *
     * @param array $values the list of items to be rendered.
     *
     * @return self
     */
    public function items(array $values): self
    {
        $new = clone $this;
        $new->items = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified if enabled or disabled the items container.
     *
     * @param bool $value The items container enable or disable, for default is `true`.
     *
     * @return self
     */
    public function itemsContainer(bool $value): self
    {
        $new = clone $this;
        $new->itemsContainer = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified items' container attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return self
     */
    public function itemsContainerAttributes(array $values): self
    {
        $new = clone $this;
        $new-> itemsContainerAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified items' container class.
     *
     * @param string $value The CSS class that will be assigned to the items' container.
     *
     * @return self
     */
    public function itemsContainerClass(string $value): self
    {
        $new = clone $this;
        Html::addCssClass($new->itemsContainerAttributes, $value);

        return $new;
    }

    /**
     * Returns a new instance with the specified items tag.
     *
     * @param string $value The tag that will be used to wrap the items.
     *
     * @return self
     */
    public function itemsTag(string $value): self
    {
        $new = clone $this;
        $new->itemsTag = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified label template.
     *
     * @param string $value The template used to render the body of a menu which is NOT a link.
     *
     * In this template, the token `{label}` will be replaced with the label of the menu item.
     *
     * This property will be overridden by the `template` option set in individual menu items via {@see items}.
     *
     * @return self
     */
    public function labelTemplate(string $value): self
    {
        $new = clone $this;
        $new->labelTemplate = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified last item CSS class.
     *
     * @param string $value The CSS class that will be assigned to the last item in the main menu or each submenu.
     *
     * @return self
     */
    public function lastItemClass(string $value): self
    {
        $new = clone $this;
        $new->lastItemClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified link attributes.
     *
     * @param array $values Attribute values indexed by attribute names.
     *
     * @return self
     */
    public function linkAttributes(array $values): self
    {
        $new = clone $this;
        $new->linkAttributes = $values;

        return $new;
    }

    /**
     * Returns a new instance with the specified link css class.
     *
     * @param string $value The CSS class that will be assigned to the link.
     *
     * @return self
     */
    public function linkClass(string $value): self
    {
        $new = clone $this;
        $new->linkClass = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified link tag.
     *
     * @param string $value The tag that will be used to wrap the link.
     *
     * @return self
     */
    public function linkTag(string $value): self
    {
        $new = clone $this;
        $new->linkTag = $value;

        return $new;
    }

    /**
     * Returns a new instance with the specified tag for rendering the menu.
     *
     * @param string $value The tag for rendering the menu.
     *
     * @return self
     */
    public function tagName(string $value): self
    {
        $new = clone $this;
        $new->tagName = $value;

        return $new;
    }

    /**
     * Renders the menu.
     *
     * @throws ReflectionException
     *
     * @return string The result of Widget execution to be outputted.
     */
    protected function run(): string
    {
        $items = $this->normalizeItems($this->items, $this->currentPath, $this->activateItems);

        if ($items === []) {
            return '';
        }

        return $this->renderMenu($items);
    }

    /**
     * Checks whether a menu item is active.
     *
     * This is done by checking match that specified in the `url` option of the menu item.
     *
     * @param string $link The link of the menu item.
     * @param bool $active Whether the menu item is active or not.
     * @param string $currentPath The current path.
     *
     * @return bool Whether the menu item is active.
     */
    private function isItemActive(string $link, string $currentPath, bool $activateItems): bool
    {
        return ($link === $currentPath) && $activateItems;
    }

    /**
     * Normalize the given array of items for the menu.
     *
     * @param array $items The items to be normalized.
     * @param string $currentPath The current path.
     * @param bool $active Should The parent be active too.
     * @param bool $activeItems Whether the item is active or not.
     *
     * @return array The normalized array of items.
     */
    private function normalizeItems(
        array $items,
        string $currentPath = '/',
        bool $activateItems = true,
        bool &$active = false,
    ): array {
        /**
         * @psalm-var array[] $items
         * @psalm-suppress RedundantConditionGivenDocblockType
         */
        foreach ($items as $i => $child) {
            if (is_array($child)) {
                /** @var array */
                $dropdown = $child['items'] ?? [];

                if ($dropdown !== []) {
                    throw new InvalidArgumentException('Dropdown menu is not supported.');
                }
                /** @var string */
                $link = $child['link'] ?? '/';
                /** @var bool */
                $active = $child['active'] ?? false;

                if ($active === false) {
                    $items[$i]['active'] = $this->isItemActive($link, $currentPath, $activateItems);
                }

                /** @var bool */
                $items[$i]['disabled'] = $child['disabled'] ?? false;
                /** @var bool */
                $items[$i]['encodeLabel'] = $child['encodeLabel'] ?? true;
                /** @var string */
                $items[$i]['icon'] = $child['icon'] ?? '';
                /** @var array */
                $items[$i]['iconAttributes'] = $child['iconAttributes'] ?? [];
                /** @var string */
                $items[$i]['iconClass'] = $child['iconClass'] ?? '';
                /** @var array */
                $items[$i]['iconContainerAttributes'] = $child['iconContainerAttributes'] ?? [];
                /** @var bool */
                $items[$i]['visible'] = $child['visible'] ?? true;
            }
        }

        return $items;
    }

    private function renderAfterContent(): string
    {
        $afterAttributes = $this->afterAttributes;

        if ($this->afterTag === '') {
            throw new InvalidArgumentException('Tag name must be a string and cannot be empty.');
        }

        return PHP_EOL . CustomTag::name($this->afterTag)
            ->addAttributes($afterAttributes)
            ->content($this->afterContent)
            ->encode(false)
            ->render();
    }

    private function renderBeforeContent(): string
    {
        $beforeAttributes = $this->beforeAttributes;

        if ($this->beforeTag === '') {
            throw new InvalidArgumentException('Tag name must be a string and cannot be empty.');
        }

        return CustomTag::name($this->beforeTag)
            ->addAttributes($beforeAttributes)
            ->content($this->beforeContent)
            ->encode(false)
            ->render();
    }

    /**
     * Renders the content of a menu item.
     *
     * Note that the container and the sub-menus are not rendered here.
     *
     * @param array $item The menu item to be rendered. Please refer to {@see items} to see what data might be in the
     * item.
     *
     * @return string The rendering result.
     */
    private function renderItem(array $item): string
    {
        /** @var string */
        $label = $item['label'];
        /** @var array */
        $linkAttributes = $item['linkAttributes'] ?? [];
        $linkAttributes = array_merge($linkAttributes, $this->linkAttributes);

        if ($this->linkClass !== '') {
            Html::addCssClass($linkAttributes, $this->linkClass);
        }

        if ($item['active']) {
            $linkAttributes['aria-current'] = 'page';
            Html::addCssClass($linkAttributes, $this->activeClass);
        }

        if ($item['disabled']) {
            Html::addCssClass($linkAttributes, $this->disabledClass);
        }

        if ($item['encodeLabel']) {
            $label = Html::encode($label);
        }

        if (isset($item['link']) && is_string($item['link'])) {
            $linkAttributes['href'] = $item['link'];
        }

        /**
         * @var string $item['icon']
         * @var string $item['iconClass']
         * @var array $item['iconAttributes']
         * @var array $item['iconContainerAttributes']
         */
        $label = $this->renderLabel(
            $label,
            $item['icon'],
            $item['iconClass'],
            $item['iconAttributes'],
            $item['iconContainerAttributes'],
        );

        if ($this->linkTag === '') {
            throw new InvalidArgumentException('Tag name must be a string and cannot be empty.');
        }

        return match (isset($linkAttributes['href'])) {
            true => CustomTag::name($this->linkTag)
                ->addAttributes($linkAttributes)
                ->content($label)
                ->encode(false)
                ->render(),
            false => $label,
        };
    }

    /**
     * Recursively renders the menu items (without the container tag).
     *
     * @param array $items The menu items to be rendered recursively.
     *
     * @throws ReflectionException
     *
     * @return string The rendering result.
     */
    private function renderItems(array $items): string
    {
        $lines = [];
        $n = count($items);

        /** @psalm-var array[] $items  */
        foreach ($items as $i => $item) {
            if ($item['visible']) {
                /** @psalm-var array|null $item['itemsContainerAttributes'] */
                $itemsContainerAttributes = array_merge(
                    $this->itemsContainerAttributes,
                    $item['itemsContainerAttributes'] ?? [],
                );

                if ($i === 0 && $this->firstItemClass !== '') {
                    Html::addCssClass($itemsContainerAttributes, $this->firstItemClass);
                }

                if ($i === $n - 1 && $this->lastItemClass !== '') {
                    Html::addCssClass($itemsContainerAttributes, $this->lastItemClass);
                }

                if ($this->itemsTag === '') {
                    throw new InvalidArgumentException('Tag name must be a string and cannot be empty.');
                }

                $menu = $this->renderItem($item);

                $lines[] = match ($this->itemsContainer) {
                    false => $menu,
                    default => CustomTag::name($this->itemsTag)
                        ->addAttributes($itemsContainerAttributes)
                        ->content($menu)
                        ->encode(false)
                        ->render(),
                };
            }
        }

        return PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL;
    }

    /**
     * @throws ReflectionException
     */
    private function renderMenu(array $items): string
    {
        $afterContent = '';
        $attributes = $this->attributes;
        $beforeContent = '';

        $content = $this->renderItems($items);

        if ($this->beforeContent !== '') {
            $beforeContent = $this->renderBeforeContent() . PHP_EOL;
        }

        if ($this->afterContent !== '') {
            $afterContent = $this->renderAfterContent();
        }

        if ($this->tagName === '') {
            throw new InvalidArgumentException('Tag name must be a string and cannot be empty.');
        }

        return match ($this->container) {
            false => $beforeContent . $content . $afterContent,
            default => $beforeContent .
                CustomTag::name($this->tagName)->addAttributes($attributes)->content($content)->encode(false) .
                $afterContent,
        };
    }

    private function renderLabel(
        string $label,
        string $icon,
        string $iconClass,
        array $iconAttributes = [],
        array $iconContainerAttributes = []
    ): string {
        $html = '';
        $iconContainerAttributes = array_merge($this->iconContainerAttributes, $iconContainerAttributes);

        if ($iconClass !== '') {
            Html::addCssClass($iconAttributes, $iconClass);
        }

        if ($icon !== '' || $iconClass !== '') {
            $i = I::tag()->addAttributes($iconAttributes)->content($icon)->encode(false)->render();
            $html = Span::tag()->addAttributes($iconContainerAttributes)->content($i)->encode(false)->render();
        }

        if ($label !== '') {
            $html .= $label;
        }

        return $html;
    }
}
