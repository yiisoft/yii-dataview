<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

/**
 * The context of a label to be rendered.
 */
final class LabelContext
{
    /**
     * @param DataField $field The field to render label for.
     * @param array|object $data The data item to use.
     */
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
    ) {
    }
}
