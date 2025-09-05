<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

/**
 * The context of a field to be rendered.
 */
final class FieldContext
{
    /**
     * @param DataField $field The field to render.
     * @param array|object $data The data item to use.
     * @param string $value The value to render.
     * @param string $label The label to render.
     */
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
        public readonly string $value,
        public readonly string $label,
    ) {
    }
}
