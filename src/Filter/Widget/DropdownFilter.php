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
    public function withOptionsData(
        array $data,
        bool $encode = true,
        array $optionsAttributes = [],
        array $groupsAttributes = []
    ): self {
        $this->select = $this->getSelect()->optionsData($data, $encode, $optionsAttributes, $groupsAttributes);
        return $this;
    }

    public function renderFilter(Context $context): string
    {
        return $this->getSelect()
            ->name($context->property)
            ->value($context->value)
            ->form($context->formId)
            ->attribute('onChange', 'this.form.submit()')
            ->render();
    }

    private function getSelect(): Select
    {
        return $this->select ?? Select::tag();
    }
}
