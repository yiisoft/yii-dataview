<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use BackedEnum;
use Yiisoft\Html\Tag\Select;

/**
 * Filter widget that renders a dropdown (select) input for filtering data.
 *
 * @psalm-import-type OptionsData from Select
 */
final class DropdownFilter extends FilterWidget
{
    private ?Select $select = null;
    private bool $submitOnChange = true;

    /**
     * Sets the options data for the dropdown.
     *
     * @param array $data Options data. The array keys are option values, and the array values are the corresponding
     * option labels. For option groups, use a nested array where the array value is an array of options.
     * Example:
     * ```php
     * [
     *     'active' => 'Active',
     *     'status' => [
     *         'pending' => 'Pending',
     *         'completed' => 'Completed',
     *     ],
     * ]
     * ```
     *
     * @param bool $encode Whether to HTML-encode option content.
     * Set to `false` if your option labels contain HTML that should be rendered.
     *
     * @param array[] $optionsAttributes Array of option attribute sets indexed by option values.
     * Example: `['active' => ['class' => 'highlight']]`
     *
     * @param array[] $groupsAttributes Array of group attribute sets indexed by group labels.
     * Example: `['status' => ['class' => 'main-group']]`
     *
     * @return self New instance with configured options.
     *
     * @see Select::optionsData()
     *
     * @psalm-param OptionsData $data
     */
    public function optionsData(
        array $data,
        bool $encode = true,
        array $optionsAttributes = [],
        array $groupsAttributes = [],
    ): self {
        $new = clone $this;
        $new->select = $this->getSelect()->optionsData($data, $encode, $optionsAttributes, $groupsAttributes);
        return $new;
    }

    /**
     * Add a set of attributes to existing tag attributes.
     * Same named attributes are replaced.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'form-select', 'data-role' => 'filter']`
     *
     * @return self New instance with added attributes.
     *
     * @see Select::addAttributes()
     */
    public function addAttributes(array $attributes): self
    {
        $new = clone $this;
        $new->select = $this->getSelect()->addAttributes($attributes);
        return $new;
    }

    /**
     * Replace attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     * Example: `['class' => 'custom-select', 'required' => true]`
     *
     * @return self New instance with replaced attributes.
     *
     * @see Select::attributes()
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->select = $this->getSelect()->attributes($attributes);
        return $new;
    }

    /**
     * Add one or more CSS classes to the `Select` tag.
     *
     * @param BackedEnum|string|null ...$class One or many CSS classes.
     */
    public function addClass(BackedEnum|string|null ...$class): self
    {
        $new = clone $this;
        $new->select = $this->getSelect()->addClass(...$class);
        return $new;
    }

    /**
     * Replace current `Select` tag CSS classes with a new set of classes.
     *
     * @param BackedEnum|string|null ...$class One or many CSS classes.
     */
    public function class(BackedEnum|string|null ...$class): self
    {
        $new = clone $this;
        $new->select = $this->getSelect()->class(...$class);
        return $new;
    }

    /**
     * Whether to render an inline `onChange="this.form.submit()"` attribute that automatically
     * submits the containing form when the dropdown's value changes. Enabled by default.
     *
     * Under a Content Security Policy `script-src` directive that does not include
     * `unsafe-inline` — the standard hardening recommendation, and required for any serious XSS
     * mitigation — browsers block this inline handler outright. Selecting an option then does
     * nothing, silently: no exception is thrown anywhere in the application, the only signal is
     * a CSP violation logged to the browser console.
     *
     * Disable it with `submitOnChange(false)` and wire up your own CSP-safe submission instead,
     * for example a delegated `change` listener in an external script targeting a `class` or
     * `data-*` marker set via {@see addAttributes()}.
     *
     * @param bool $enabled Whether to render the inline auto-submit handler.
     *
     * @return self New instance with the given auto-submit setting.
     */
    public function submitOnChange(bool $enabled): self
    {
        $new = clone $this;
        $new->submitOnChange = $enabled;
        return $new;
    }

    /**
     * Renders the dropdown filter with the given context.
     *
     * @param Context $context The filter context.
     *
     * @return string The rendered HTML select element.
     */
    public function renderFilter(Context $context): string
    {
        $select = $this->getSelect()
            ->name($context->property)
            ->form($context->formId);

        if ($this->submitOnChange) {
            $select = $select->attribute('onChange', 'this.form.submit()');
        }

        if ($context->value !== null) {
            $select = $select->value($context->value);
        }

        return $select->render();
    }

    /**
     * Gets the select instance, creating a new one if not set.
     *
     * The default select has an empty prompt option.
     *
     * @return Select The HTML select instance.
     */
    private function getSelect(): Select
    {
        return $this->select ?? (new Select())->prompt('');
    }
}
