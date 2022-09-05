<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column;

use JsonException;
use Yiisoft\Html\Tag\Input;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 */
final class CheckboxColumn extends Column
{
    private bool $multiple = true;

    /**
     * Return new instance with the multiple flag is set to false.
     *
     * @return self
     */
    public function notMultiple(): self
    {
        $new = clone $this;
        $new->multiple = false;

        return $new;
    }

    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     *
     * @throws JsonException
     *
     * @return string
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        if ($this->getContent() !== null) {
            return parent::renderDataCellContent($data, $key, $index);
        }

        $contentAttributes = $this->getContentAttributes();
        $name = null;
        $value = null;

        if (!array_key_exists('value', $contentAttributes)) {
            $value = is_array($key)
                ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string) $key;
        }

        if (!array_key_exists('name', $contentAttributes)) {
            $name = 'checkbox-selection';
        }

        return Input::checkbox($name, $value)->addAttributes($contentAttributes)->render();
    }

    /**
     * Renders the header cell content.
     *
     * The default implementation simply renders {@see header}.
     * This method may be overridden to customize the rendering of the header cell.
     *
     * @return string
     */
    protected function renderHeaderCellContent(): string
    {
        if ($this->getLabel() !== '' || $this->multiple === false) {
            return parent::renderHeaderCellContent();
        }

        return Input::checkbox()
            ->addAttributes(['class' => 'select-on-check-all'])
            ->name('checkbox-selection-all')
            ->value(1)
            ->render();
    }
}
