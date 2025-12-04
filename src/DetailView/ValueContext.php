<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

/**
 * The context for value rendering.
 */
final class ValueContext
{
    /**
     * @param DataField $field The field to render.
     * @param array|object $data The data item to use.
     * @param string $value The value to render.
     */
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
        public string $value,
    ) {}
}
