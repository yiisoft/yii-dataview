<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Html\Tag\Select;

final class DropdownFilter extends FilterWidget
{
    private ?Select $select = null;

    /**
     * @param array $data Options data. The array keys are option values, and the array values are the corresponding
     * option labels.
     *
     * @param bool $encode Whether option content should be HTML-encoded.
     * @param array[] $optionsAttributes Array of option attribute sets indexed by option values from {@see $data}.
     * @param array[] $groupsAttributes Array of group attribute sets indexed by group labels from {@see $data}.
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
     *
     * @see Select::addAttributes()
     */
    public function addAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->select = $this->getSelect()->addAttributes($attributes);
        return $new;
    }

    /**
     * Replace attributes with a new set.
     *
     * @param array $attributes Name-value set of attributes.
     *
     * @see Select::attributes()
     */
    public function attributes(array $attributes): self
    {
        $new = clone $this;
        $new->select = $this->getSelect()->attributes($attributes);
        return $new;
    }

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

    private function getSelect(): Select
    {
        return $this->select ?? Select::tag()->prompt('');
    }
}
