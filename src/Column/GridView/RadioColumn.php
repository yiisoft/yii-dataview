<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\GridView;

use Yiisoft\Html\Tag\Input;

use function json_encode;

/**
 * RadioButtonColumn displays a column of radio buttons in a grid view.
 */
final class RadioColumn extends AbstractColumn
{
    /**
     * Renders the data cell content.
     *
     * @param array|object $data The data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data in the data provider.
     */
    protected function renderDataCellContent(array|object $data, mixed $key, int $index): string
    {
        if ($this->getContent() !== null) {
            return parent::renderDataCellContent($data, $key, $index);
        }

        $contentAttributes = $this->getContentAttributes();
        $name = null;
        $value = null;

        if (!array_key_exists('name', $contentAttributes)) {
            $name = 'radio-selection';
        }

        if (!array_key_exists('value', $contentAttributes)) {
            $value = is_array($key)
                ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string) $key;
        }

        return Input::radio($name, $value)->addAttributes($contentAttributes)->render();
    }
}
