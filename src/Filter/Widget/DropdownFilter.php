<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Html\Tag\Select;

/**
 * Filter widget that renders a dropdown (select) input for filtering data.
 *
 * This widget creates an HTML select element that can be used for filtering data
 * based on predefined options. It supports:
 * - Option groups
 * - HTML encoding control
 * - Custom attributes for options and groups
 * - Auto-submit on change
 *
 * Example usage:
 * ```php
 * echo DropdownFilter::widget()
 *     ->optionsData([
 *         'active' => 'Active Users',
 *         'inactive' => 'Inactive Users',
 *     ])
 *     ->addAttributes(['class' => 'form-select'])
 *     ->withContext(new Context('status', 'active', 'filter-form'));
 * ```
 *
 * The above example will render:
 * ```html
 * <select class="form-select" name="status" form="filter-form" onChange="this.form.submit()">
 *     <option value=""></option>
 *     <option value="active" selected>Active Users</option>
 *     <option value="inactive">Inactive Users</option>
 * </select>
 * ```
 */
final class DropdownFilter extends FilterWidget
{
    private ?Select $select = null;

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
     * Set to false if your option labels contain HTML that should be rendered.
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
     * @psalm-param array<array-key, string|array<array-key,string>> $data
     */
    public function optionsData(
        array $data,
        bool $encode = true,
        array $optionsAttributes = [],
        array $groupsAttributes = []
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
     * Renders the dropdown filter with the given context.
     *
     * Uses the context to set:
     * - name attribute from context property
     * - value attribute from context value
     * - form attribute from context formId
     *
     * Additionally adds an onChange event handler to auto-submit the form
     * when a new option is selected.
     *
     * @param Context $context The filter context.
     *
     * @return string The rendered HTML select element.
     */
    public function renderFilter(Context $context): string
    {
        $select = $this->getSelect()
            ->name($context->property)
            ->form($context->formId)
            ->attribute('onChange', 'this.form.submit()');

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
        return $this->select ?? Select::tag()->prompt('');
    }
}
